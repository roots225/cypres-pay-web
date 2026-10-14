<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request, ?string $slug = null)
    {
        $currentCategory = null;
        if ($slug) {
            $currentCategory = Category::where('slug', $slug)->first();
        }

        return view('catalog', [
            'categorySlug' => $slug,
            'currentCategory' => $currentCategory,
        ]);
    }

    public function kits()
    {
        return view('catalog', [
            'categorySlug' => 'kits-maison-et-cuisine',
            'isKitsPage' => true,
        ]);
    }

    public function flash()
    {
        return view('catalog', [
            'categorySlug' => null,
            'isFlashPage' => true,
        ]);
    }
}
