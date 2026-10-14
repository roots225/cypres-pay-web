<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'medias';

    protected $fillable = [
        'user_id',
        'name',
        'file_name',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'extension',
        'type',
        'size',
        'width',
        'height',
        'alt',
        'caption',
        'folder',
        'metadata',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'metadata' => 'array',
    ];

    protected $appends = [
        'url',
        'size_human',
        'icon',
        'badge_color',
        'dimensions_formatted',
    ];

    /**
     * Suppression automatique du fichier sur le disque lors de la suppression de l'enregistrement.
     */
    protected static function booted()
    {
        static::deleting(function (Media $media) {
            try {
                if ($media->path && Storage::disk($media->disk)->exists($media->path)) {
                    Storage::disk($media->disk)->delete($media->path);
                }
            } catch (\Throwable $e) {
                // Ignore silent disk delete failure
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * URL publique d'accès au fichier.
     */
    public function getUrlAttribute(): string
    {
        if (empty($this->path)) {
            return '';
        }

        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }

        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Taille formatée lisible pour l'humain (Ko, Mo, Go).
     */
    public function getSizeHumanAttribute(): string
    {
        $bytes = (int) $this->size;

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
    }

    /**
     * Dimensions en pixels (ex: "1920 × 1080 px").
     */
    public function getDimensionsFormattedAttribute(): ?string
    {
        if ($this->width && $this->height) {
            return "{$this->width} × {$this->height} px";
        }
        return null;
    }

    /**
     * Icône Lucide selon le type MIME et le type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'image' => 'image',
            'video' => 'video',
            'audio' => 'music',
            'document' => match ($this->extension) {
                'pdf' => 'file-text',
                'doc', 'docx' => 'file-text',
                'xls', 'xlsx', 'csv' => 'file-spreadsheet',
                'ppt', 'pptx' => 'presentation',
                default => 'file',
            },
            'archive' => 'archive',
            default => 'file',
        };
    }

    /**
     * Couleur du badge pour l'interface UI Cypres Pay.
     */
    public function getBadgeColorAttribute(): array
    {
        return match ($this->type) {
            'image' => [
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'badge' => 'bg-emerald-500',
                'label' => 'Image',
            ],
            'video' => [
                'bg' => 'bg-purple-50 text-purple-700 border-purple-200',
                'badge' => 'bg-purple-500',
                'label' => 'Vidéo',
            ],
            'audio' => [
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'badge' => 'bg-amber-500',
                'label' => 'Audio',
            ],
            'document' => [
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200',
                'badge' => 'bg-blue-500',
                'label' => 'Document',
            ],
            'archive' => [
                'bg' => 'bg-orange-50 text-orange-700 border-orange-200',
                'badge' => 'bg-orange-500',
                'label' => 'Archive',
            ],
            default => [
                'bg' => 'bg-slate-50 text-slate-700 border-slate-200',
                'badge' => 'bg-slate-500',
                'label' => 'Fichier',
            ],
        };
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isPdf(): bool
    {
        return $this->extension === 'pdf' || $this->mime_type === 'application/pdf';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    /**
     * Détermine le type de média à partir du mime_type et de l'extension.
     */
    public static function determineType(string $mimeType, ?string $extension = null): string
    {
        $extension = strtolower($extension ?? '');

        if (str_starts_with($mimeType, 'image/') || in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif', 'bmp'])) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/') || in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv'])) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/') || in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'])) {
            return 'audio';
        }

        if (
            str_contains($mimeType, 'pdf') ||
            str_contains($mimeType, 'word') ||
            str_contains($mimeType, 'excel') ||
            str_contains($mimeType, 'spreadsheet') ||
            str_contains($mimeType, 'presentation') ||
            str_starts_with($mimeType, 'text/') ||
            in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'rtf', 'ppt', 'pptx'])
        ) {
            return 'document';
        }

        if (
            str_contains($mimeType, 'zip') ||
            str_contains($mimeType, 'compressed') ||
            str_contains($mimeType, 'tar') ||
            in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'])
        ) {
            return 'archive';
        }

        return 'other';
    }

    // Scopes
    public function scopeType($query, $type)
    {
        if (empty($type) || $type === 'all') {
            return $query;
        }

        if (is_array($type)) {
            return $query->whereIn('type', $type);
        }

        return $query->where('type', $type);
    }

    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('original_name', 'like', "%{$term}%")
              ->orWhere('alt', 'like', "%{$term}%")
              ->orWhere('caption', 'like', "%{$term}%");
        });
    }

    public function scopeFolder($query, $folder)
    {
        if (empty($folder) || $folder === 'all') {
            return $query;
        }

        return $query->where('folder', $folder);
    }
}
