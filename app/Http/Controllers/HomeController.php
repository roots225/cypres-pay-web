<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $flashProducts = Product::where('is_flash', true)
            ->where('en_stock', true)
            ->take(4)
            ->get();

        $kitProducts = Product::where('is_kit', true)
            ->where('en_stock', true)
            ->take(4)
            ->get();

        $categories = Category::where('is_featured', true)
            ->orderBy('ordre')
            ->take(8)
            ->get();

        return view('home', compact('flashProducts', 'kitProducts', 'categories'));
    }
}
