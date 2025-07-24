<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class FeatureProductController extends Controller
{
    public function index()
    {
        return view('frontend.feature.products');
    }

    public function products()
    {
        $products = Product::with('reviews', 'comboProducts')->orderBy('created_at', 'desc')->where('product_type', 'feature')->where('status', 1)->get();
        return response()->json($products);
    }

    public function list()
    {
        $products = Product::with('reviews')->orderBy('created_at', 'desc')->where('product_type', 'feature')->where('status', 1)->get();
        return response()->json([
            'status' => 200,
            'products' => $products
        ]);
    }
}
