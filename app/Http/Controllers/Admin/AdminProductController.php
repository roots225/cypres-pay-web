<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $categoryId = $request->get('category_id');
        $filter = $request->get('filter'); // 'flash', 'kit', 'vedette', 'low_stock'

        $query = Product::with('category');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('marque', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($filter === 'flash') {
            $query->where('is_flash', true);
        } elseif ($filter === 'kit') {
            $query->where('is_kit', true);
        } elseif ($filter === 'vedette') {
            $query->where('is_vedette', true);
        } elseif ($filter === 'low_stock') {
            $query->where('stock', '<=', 5);
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::orderBy('nom')->get();

        return view('admin.products.index', compact('products', 'categories', 'search', 'categoryId', 'filter'));
    }

    public function create()
    {
        $categories = Category::orderBy('nom')->get();
        return view('admin.products.form', [
            'product' => new Product([
                'stock' => 10,
                'en_stock' => true,
                'is_kit' => false,
                'is_flash' => false,
                'is_vedette' => false,
            ]),
            'isEdit' => false,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:50|unique:products,reference',
            'nom' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'marque' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'prix_vente' => 'required|integer|min:1000',
            'prix_cash' => 'nullable|integer|min:500',
            'mensualite_indicative' => 'nullable|integer',
            'stock' => 'required|integer|min:0',
            'image_principale' => 'required|string',
            'galerie_images' => 'nullable|array',
            'galerie_images.*' => 'string',
            'description_courte' => 'nullable|string|max:500',
            'description_complete' => 'nullable|string',
            'is_kit' => 'boolean',
            'is_flash' => 'boolean',
            'is_vedette' => 'boolean',
            'en_stock' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['reference'] . ' ' . $validated['nom']);
        }

        // Remise cash automatique de -7% si non spécifié
        if (empty($validated['prix_cash'])) {
            $validated['prix_cash'] = (int) round($validated['prix_vente'] * 0.93);
        }

        // Mensualité indicative (sur 12 mois) si non spécifiée
        if (empty($validated['mensualite_indicative'])) {
            $validated['mensualite_indicative'] = (int) round($validated['prix_vente'] / 12);
        }

        $gallery = array_values(array_filter((array) $request->input('galerie_images', []), fn($img) => !empty(trim((string)$img))));
        $primary = $request->input('image_principale');

        if (empty($primary) && !empty($gallery)) {
            $primary = $gallery[0];
        }

        // Retirer l'image principale de la galerie pour éviter les doublons
        $gallery = array_values(array_filter($gallery, fn($img) => $img !== $primary));

        $validated['image_principale'] = $primary ?: 'https://placehold.co/600x600/f8fafc/94a3b8?text=Image+Produit';
        $validated['galerie_images'] = $gallery;

        $validated['is_kit'] = $request->boolean('is_kit');
        $validated['is_flash'] = $request->boolean('is_flash');
        $validated['is_vedette'] = $request->boolean('is_vedette');
        $validated['en_stock'] = $request->boolean('en_stock');

        $product = Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', "Le produit [{$product->reference}] {$product->nom} a été créé avec succès.");
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('nom')->get();
        return view('admin.products.form', [
            'product' => $product,
            'isEdit' => true,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($product->id)],
            'nom' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'marque' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'prix_vente' => 'required|integer|min:1000',
            'prix_cash' => 'nullable|integer|min:500',
            'mensualite_indicative' => 'nullable|integer',
            'stock' => 'required|integer|min:0',
            'image_principale' => 'required|string',
            'galerie_images' => 'nullable|array',
            'galerie_images.*' => 'string',
            'description_courte' => 'nullable|string|max:500',
            'description_complete' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['reference'] . ' ' . $validated['nom']);
        }

        if (empty($validated['prix_cash'])) {
            $validated['prix_cash'] = (int) round($validated['prix_vente'] * 0.93);
        }

        if (empty($validated['mensualite_indicative'])) {
            $validated['mensualite_indicative'] = (int) round($validated['prix_vente'] / 12);
        }

        $gallery = array_values(array_filter((array) $request->input('galerie_images', []), fn($img) => !empty(trim((string)$img))));
        $primary = $request->input('image_principale');

        if (empty($primary) && !empty($gallery)) {
            $primary = $gallery[0];
        }

        // Retirer l'image principale de la galerie pour éviter les doublons
        $gallery = array_values(array_filter($gallery, fn($img) => $img !== $primary));

        $validated['image_principale'] = $primary ?: 'https://placehold.co/600x600/f8fafc/94a3b8?text=Image+Produit';
        $validated['galerie_images'] = $gallery;

        $validated['is_kit'] = $request->boolean('is_kit');
        $validated['is_flash'] = $request->boolean('is_flash');
        $validated['is_vedette'] = $request->boolean('is_vedette');
        $validated['en_stock'] = $request->boolean('en_stock');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', "Le produit [{$product->reference}] {$product->nom} a été mis à jour.");
    }

    public function toggle(Product $product, string $field)
    {
        $allowedFields = ['is_kit', 'is_flash', 'is_vedette', 'en_stock'];
        if (! in_array($field, $allowedFields)) {
            abort(400);
        }

        $product->$field = ! $product->$field;
        $product->save();

        $status = $product->$field ? 'activé' : 'désactivé';
        return back()->with('success', "Attribut '{$field}' {$status} pour {$product->nom}.");
    }

    public function destroy(Product $product)
    {
        $ref = $product->reference;
        $name = $product->nom;
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Le produit [{$ref}] {$name} a été supprimé.");
    }
}
