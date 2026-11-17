<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UserDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // ✅ Cache recent products — busts every 5 minutes
        $recentProducts = Cache::remember('recent_products', 300, function () {
            return Product::with('category', 'brand')
                ->latest()
                ->take(6)
                ->get();
        });

        // ✅ Cache total categories — busts when admin changes categories
        $totalCategories = Cache::remember('categories_count', 3600, function () {
            return Category::count();
        });

        // ✅ Cart count — user-specific, short TTL or skip cache entirely
        $cartCount = CartItem::where('user_id', $userId)->sum('quantity');

        // ✅ Recent cart items for dashboard widget — just take(5), no pagination
        $cartItems = CartItem::where('user_id', $userId)
            ->with('product')
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact(
            'recentProducts',
            'totalCategories',
            'cartCount',
            'cartItems'
        ));
    }
}