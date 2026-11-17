<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'brand');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products   = $query->latest()->paginate(12)->withQueryString();
        $categories = Cache::remember('categories', 3600, fn() => Category::all());
        $brands     = Cache::remember('brands', 3600, fn() => Brand::all());
        $cartItems  = CartItem::where('user_id', auth()->id())
                        ->pluck('quantity', 'product_id');

        return view('user.products.index', compact('products', 'categories', 'brands', 'cartItems'));
    }

    public function show($id)
    {
        $product = Product::with('category', 'brand')->findOrFail($id);
        return view('user.products.show', compact('product'));
    }
}