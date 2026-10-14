<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminMediaController extends Controller
{
    /**
     * Liste des médias (Interface Web ou JSON pour Picker Modal).
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        $folder = $request->get('folder');
        $search = $request->get('q');
        $sort = $request->get('sort', 'latest');
        $perPage = (int) $request->get('per_page', 24);

        $query = Media::query();

        if ($type && $type !== 'all') {
            if (str_contains($type, ',')) {
                $query->type(explode(',', $type));
            } else {
                $query->type($type);
            }
        }

        if ($folder && $folder !== 'all') {
            $query->folder($folder);
        }

        if ($search) {
            $query->search($search);
        }

        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'size_desc':
                $query->orderBy('size', 'desc');
                break;
            case 'size_asc':
                $query->orderBy('size', 'asc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $medias = $query->paginate($perPage)->withQueryString();

        // Réponse API / JSON pour le Media Picker Modal et requêtes AJAX
        if ($request->wantsJson() || $request->ajax() || $request->get('format') === 'json') {
            return response()->json([
                'success' => true,
                'data' => $medias->items(),
                'pagination' => [
                    'current_page' => $medias->currentPage(),
                    'last_page' => $medias->lastPage(),
                    'per_page' => $medias->perPage(),
                    'total' => $medias->total(),
                    'has_more' => $medias->hasMorePages(),
                ],
            ]);
        }

        // Statistiques globales pour la page Media Manager
        $stats = $this->calculateStats();
        $folders = Media::select('folder')
            ->distinct()
            ->whereNotNull('folder')
            ->pluck('folder')
            ->toArray();

        return view('admin.media.index', compact('medias', 'stats', 'folders', 'type', 'folder', 'search', 'sort'));
    }

    /**
     * Téléversement de fichiers (simple ou multiple, drag & drop ou bouton).
     */
    public function store(Request $request)
    {
        $uploadedMedias = [];
        $files = [];

        if ($request->hasFile('files')) {
            $files = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $files = [$request->file('file')];
        }

        if (empty($files) && ! $request->has('base64')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Aucun fichier reçu.'], 422);
            }
            return back()->with('error', 'Aucun fichier sélectionné.');
        }

        $folder = $request->input('folder', 'general');
        $yearMonth = date('Y/m');
        $targetDirectory = "media/{$yearMonth}";

        foreach ($files as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
            $mimeType = $file->getMimeType() ?: 'application/octet-stream';
            $size = $file->getSize();

            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $cleanSlug = Str::slug($baseName) ?: 'media';
            $uniqueFileName = $cleanSlug . '_' . time() . '_' . Str::lower(Str::random(6)) . '.' . $extension;

            // Stockage sur le disque public
            $storedPath = $file->storeAs($targetDirectory, $uniqueFileName, 'public');

            $type = Media::determineType($mimeType, $extension);
            $width = null;
            $height = null;

            if ($type === 'image' && in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'])) {
                try {
                    $dimensions = @getimagesize($file->getRealPath());
                    if ($dimensions) {
                        $width = $dimensions[0] ?? null;
                        $height = $dimensions[1] ?? null;
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }

            $media = Media::create([
                'user_id' => Auth::id(),
                'name' => str_replace(['-', '_'], ' ', ucfirst($baseName)),
                'file_name' => $uniqueFileName,
                'original_name' => $originalName,
                'disk' => 'public',
                'path' => $storedPath,
                'mime_type' => $mimeType,
                'extension' => $extension,
                'type' => $type,
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'alt' => $request->input('alt') ?: str_replace(['-', '_'], ' ', ucfirst($baseName)),
                'caption' => $request->input('caption'),
                'folder' => $folder,
            ]);

            $uploadedMedias[] = $media;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => count($uploadedMedias) . ' fichier(s) téléversé(s) avec succès.',
                'data' => $uploadedMedias,
                'media' => $uploadedMedias[0] ?? null,
            ], 201);
        }

        return back()->with('success', count($uploadedMedias) . ' fichier(s) téléversé(s) avec succès.');
    }

    /**
     * Recadrage et rognage d'une image (Crop Tool).
     */
    public function crop(Request $request, Media $media)
    {
        $request->validate([
            'cropped_image' => 'required|string', // Base64 data URI (data:image/jpeg;base64,...)
            'save_as_new' => 'nullable|boolean',
        ]);

        $base64Data = $request->input('cropped_image');
        $saveAsNew = $request->boolean('save_as_new', true);

        // Décodage du Base64
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $typeMatch)) {
            $imageExtension = strtolower($typeMatch[1]);
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $base64Data = base64_decode($base64Data);

            if ($base64Data === false) {
                return response()->json(['success' => false, 'message' => 'Données de l\'image recadrée invalides.'], 422);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Format d\'image non reconnu.'], 422);
        }

        if ($imageExtension === 'jpeg') {
            $imageExtension = 'jpg';
        }

        $yearMonth = date('Y/m');
        $targetDirectory = "media/{$yearMonth}";
        $cleanSlug = Str::slug(pathinfo($media->name, PATHINFO_FILENAME)) ?: 'cropped';
        $newFileName = $cleanSlug . '_crop_' . time() . '_' . Str::lower(Str::random(6)) . '.' . $imageExtension;
        $newPath = "{$targetDirectory}/{$newFileName}";

        // Enregistrement physique du fichier rogné sur le disque
        Storage::disk('public')->put($newPath, $base64Data);

        $newSize = strlen($base64Data);
        $width = null;
        $height = null;

        try {
            $fullDiskPath = Storage::disk('public')->path($newPath);
            $dimensions = @getimagesize($fullDiskPath);
            if ($dimensions) {
                $width = $dimensions[0] ?? null;
                $height = $dimensions[1] ?? null;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        if ($saveAsNew) {
            // Créer une nouvelle entrée dans la médiathèque
            $resultMedia = Media::create([
                'user_id' => Auth::id(),
                'name' => $media->name . ' (Rogné)',
                'file_name' => $newFileName,
                'original_name' => 'crop_' . $media->original_name,
                'disk' => 'public',
                'path' => $newPath,
                'mime_type' => 'image/' . ($imageExtension === 'jpg' ? 'jpeg' : $imageExtension),
                'extension' => $imageExtension,
                'type' => 'image',
                'size' => $newSize,
                'width' => $width,
                'height' => $height,
                'alt' => $media->alt ?: $media->name,
                'caption' => $media->caption,
                'folder' => $media->folder ?: 'general',
                'metadata' => [
                    'cropped_from_id' => $media->id,
                    'cropped_at' => now()->toIso8601String(),
                ],
            ]);
        } else {
            // Remplacer l'image existante : supprimer l'ancien fichier
            try {
                if (Storage::disk($media->disk)->exists($media->path)) {
                    Storage::disk($media->disk)->delete($media->path);
                }
            } catch (\Throwable $e) {
                // ignore
            }

            $media->update([
                'file_name' => $newFileName,
                'path' => $newPath,
                'mime_type' => 'image/' . ($imageExtension === 'jpg' ? 'jpeg' : $imageExtension),
                'extension' => $imageExtension,
                'size' => $newSize,
                'width' => $width,
                'height' => $height,
                'metadata' => array_merge($media->metadata ?? [], [
                    'last_cropped_at' => now()->toIso8601String(),
                ]),
            ]);

            $resultMedia = $media->fresh();
        }

        return response()->json([
            'success' => true,
            'message' => 'L\'image a été recadrée avec succès.',
            'media' => $resultMedia,
        ]);
    }

    /**
     * Mise à jour des métadonnées d'un média (nom, alt, légende, dossier).
     */
    public function update(Request $request, Media $media)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alt' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'folder' => 'nullable|string|max:50',
        ]);

        $media->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Média mis à jour avec succès.',
                'media' => $media->fresh(),
            ]);
        }

        return back()->with('success', 'Média mis à jour.');
    }

    /**
     * Suppression unitaire d'un média.
     */
    public function destroy(Request $request, Media $media)
    {
        $name = $media->name;
        $media->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Le média \"{$name}\" a été supprimé.",
            ]);
        }

        return back()->with('success', "Le média \"{$name}\" a été supprimé.");
    }

    /**
     * Suppression groupée de médias sélectionnés.
     */
    public function batchDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:medias,id',
        ]);

        $ids = $request->input('ids');
        $medias = Media::whereIn('id', $ids)->get();
        $count = $medias->count();

        foreach ($medias as $media) {
            $media->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} média(s) supprimé(s) avec succès.",
            ]);
        }

        return back()->with('success', "{$count} média(s) supprimé(s) avec succès.");
    }

    /**
     * Statistiques globales de stockage.
     */
    public function stats()
    {
        return response()->json($this->calculateStats());
    }

    /**
     * Calcul des métriques de stockage.
     */
    private function calculateStats(): array
    {
        $totalCount = Media::count();
        $totalSize = (int) Media::sum('size');

        $byType = Media::select('type', DB::raw('count(*) as count'), DB::raw('sum(size) as size'))
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $formatBytes = function ($bytes) {
            if ($bytes >= 1073741824) {
                return number_format($bytes / 1073741824, 2, ',', ' ') . ' Go';
            }
            if ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 1, ',', ' ') . ' Mo';
            }
            if ($bytes >= 1024) {
                return number_format($bytes / 1024, 0, ',', ' ') . ' Ko';
            }
            return $bytes . ' o';
        };

        return [
            'total_count' => $totalCount,
            'total_size' => $totalSize,
            'total_size_human' => $formatBytes($totalSize),
            'types' => [
                'image' => [
                    'count' => $byType->get('image')->count ?? 0,
                    'size' => (int) ($byType->get('image')->size ?? 0),
                    'size_human' => $formatBytes((int) ($byType->get('image')->size ?? 0)),
                ],
                'document' => [
                    'count' => $byType->get('document')->count ?? 0,
                    'size' => (int) ($byType->get('document')->size ?? 0),
                    'size_human' => $formatBytes((int) ($byType->get('document')->size ?? 0)),
                ],
                'video' => [
                    'count' => $byType->get('video')->count ?? 0,
                    'size' => (int) ($byType->get('video')->size ?? 0),
                    'size_human' => $formatBytes((int) ($byType->get('video')->size ?? 0)),
                ],
                'audio' => [
                    'count' => $byType->get('audio')->count ?? 0,
                    'size' => (int) ($byType->get('audio')->size ?? 0),
                    'size_human' => $formatBytes((int) ($byType->get('audio')->size ?? 0)),
                ],
                'archive' => [
                    'count' => $byType->get('archive')->count ?? 0,
                    'size' => (int) ($byType->get('archive')->size ?? 0),
                    'size_human' => $formatBytes((int) ($byType->get('archive')->size ?? 0)),
                ],
                'other' => [
                    'count' => $byType->get('other')->count ?? 0,
                    'size' => (int) ($byType->get('other')->size ?? 0),
                    'size_human' => $formatBytes((int) ($byType->get('other')->size ?? 0)),
                ],
            ],
        ];
    }
}
