<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('ordre')
            ->orderBy('nom')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'icone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        $validated['icone'] = $validated['icone'] ?: 'category';
        $validated['ordre'] = $validated['ordre'] ?? 0;
        $validated['is_featured'] = $request->boolean('is_featured');

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "La catégorie '{$validated['nom']}' a été créée avec succès.");
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:100',
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('categories')->ignore($category->id)],
            'icone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:255',
            'ordre' => 'nullable|integer',
            'is_featured' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nom']);
        }

        $validated['icone'] = $validated['icone'] ?: 'category';
        $validated['ordre'] = $validated['ordre'] ?? 0;
        $validated['is_featured'] = $request->boolean('is_featured');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "La catégorie '{$category->nom}' a été mise à jour.");
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', "Impossible de supprimer la catégorie '{$category->nom}' car elle contient encore des produits.");
        }

        $nom = $category->nom;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "La catégorie '{$nom}' a été supprimée.");
    }
}
