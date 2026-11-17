<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // ✅ Get cart items
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product.category', 'product.brand')
            ->get();

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json([
            'success'    => true,
            'cart_items' => $cartItems->map(fn($item) => [
                'id'       => $item->id,
                'quantity' => $item->quantity,
                'subtotal' => number_format($item->product->price * $item->quantity, 2),
                'product'  => [
                    'id'       => $item->product->id,
                    'name'     => $item->product->name,
                    'price'    => $item->product->price,
                    'stock'    => $item->product->stock,
                    'image'    => $item->product->image
                                    ? asset('storage/' . $item->product->image)
                                    : null,
                    'category' => $item->product->category->name,
                    'brand'    => $item->product->brand->name,
                ],
            ]),
            'total'      => number_format($total, 2),
            'item_count' => $cartItems->sum('quantity'),
        ]);
    }

    // ✅ Add to cart
    public function add(Request $request, Product $product)
    {
        // Stock check
        if ($product->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Product is out of stock.',
            ], 422);
        }

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        // Check cart quantity against stock
        if ($cartItem && $cartItem->quantity >= $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available. Only ' . $product->stock . ' items in stock.',
            ], 422);
        }

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            CartItem::create([
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'quantity'   => 1,
            ]);
        }

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success'    => true,
            'message'    => 'Product added to cart!',
            'item_count' => $count,
        ]);
    }

    // ✅ Decrement quantity
    public function decrement(Request $request, Product $product)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.',
            ], 404);
        }

        if ($cartItem->quantity > 1) {
            $cartItem->decrement('quantity');
            $message = 'Cart updated!';
        } else {
            $cartItem->delete();
            $message = 'Item removed from cart!';
        }

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success'    => true,
            'message'    => $message,
            'item_count' => $count,
        ]);
    }

    // ✅ Update quantity
    public function update(Request $request, CartItem $cartItem)
    {
        // Ownership check
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Stock check
        if ($request->quantity > $cartItem->product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Only ' . $cartItem->product->stock . ' items available.',
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');
        $total = CartItem::where('user_id', Auth::id())
            ->with('product')->get()
            ->sum(fn($i) => $i->product->price * $i->quantity);

        return response()->json([
            'success'    => true,
            'message'    => 'Cart updated!',
            'item_count' => $count,
            'subtotal'   => number_format($cartItem->product->price * $request->quantity, 2),
            'total'      => number_format($total, 2),
        ]);
    }

    // ✅ Remove item
    public function remove(CartItem $cartItem)
    {
        // Ownership check
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $cartItem->delete();

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');
        $total = CartItem::where('user_id', Auth::id())
            ->with('product')->get()
            ->sum(fn($i) => $i->product->price * $i->quantity);

        return response()->json([
            'success'    => true,
            'message'    => 'Item removed from cart!',
            'item_count' => $count,
            'total'      => number_format($total, 2),
        ]);
    }

    // ✅ Clear cart
    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();

        return response()->json([
            'success'    => true,
            'message'    => 'Cart cleared!',
            'item_count' => 0,
            'total'      => '0.00',
        ]);
    }
}