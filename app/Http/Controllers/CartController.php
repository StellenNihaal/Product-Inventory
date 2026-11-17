<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product.category', 'product.brand')
            ->get();

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('user.cart.index', compact('cartItems', 'total'));
    }

    public function add(AddToCartRequest $request, Product $product)
    {
        // Authorization (user role check) handled by AddToCartRequest

        // ✅ Stock check
        if ($product->stock <= 0) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Out of stock'], 422);
            }
            return back()->with('error', 'Product is out of stock.');
        }

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        // ✅ Check cart quantity against stock
        if ($cartItem && $cartItem->quantity >= $product->stock) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Not enough stock available'], 422);
            }
            return back()->with('error', 'Not enough stock available.');
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

        Log::info('Product added to cart', [
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
        ]);

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');

        if ($request->ajax()) {
            return response()->json(['count' => $count]);
        }

        return back()->with('success', 'Product added to cart!');
    }

    public function decrement(Request $request, Product $product)
    {
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            } else {
                $cartItem->delete();
            }
        }

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');

        if ($request->ajax()) {
            return response()->json(['count' => $count]);
        }

        return back()->with('success', 'Cart updated!');
    }

    public function update(UpdateCartRequest $request, CartItem $cartItem)
    {
        // Ownership check (user_id match) handled by UpdateCartRequest::authorize()
        // Quantity validation handled by UpdateCartRequest::rules()

        // ✅ Stock check on update
        if ($request->validated()['quantity'] > $cartItem->product->stock) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Only ' . $cartItem->product->stock . ' items available'
                ], 422);
            }
            return back()->with('error', 'Not enough stock.');
        }

        $cartItem->update(['quantity' => $request->validated()['quantity']]);

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');
        $total = CartItem::where('user_id', Auth::id())
            ->with('product')->get()
            ->sum(fn($i) => $i->product->price * $i->quantity);

        if ($request->ajax()) {
            return response()->json([
                'count'    => $count,
                'subtotal' => number_format($cartItem->product->price * $request->validated()['quantity'], 2),
                'total'    => number_format($total, 2),
            ]);
        }

        return back()->with('success', 'Cart updated!');
    }

    public function remove(CartItem $cartItem)
    {
        // ✅ Ownership check via Policy
        $this->authorize('modify', $cartItem);

        $cartItem->delete();

        $count = CartItem::where('user_id', Auth::id())->sum('quantity');
        $total = CartItem::where('user_id', Auth::id())
            ->with('product')->get()
            ->sum(fn($i) => $i->product->price * $i->quantity);

        if (request()->ajax()) {
            return response()->json([
                'count' => $count,
                'total' => number_format($total, 2),
            ]);
        }

        return back()->with('success', 'Item removed!');
    }

    public function clear()
    {
        CartItem::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Cart cleared!');
    }
}