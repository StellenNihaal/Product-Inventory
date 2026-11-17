<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    // ✅ Just show the page — no PaymentIntent created here
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);

        return view('user.checkout', compact('cartItems', 'total'));
    }

    // ✅ Called via AJAX when user clicks Pay — creates intent & confirms
    public function createIntent(Request $request)
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Cart is empty.'], 400);
        }

        $total      = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $productIds = $cartItems->pluck('product_id')->join(',');

        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = PaymentIntent::create([
            'amount'   => (int) ($total * 100),
            'currency' => 'inr',
            'metadata' => [
                'user_id'     => Auth::id(),
                'product_ids' => $productIds,
            ],
        ]);

        // ✅ Return client_secret to JS
        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            'payment_intent_id' => $paymentIntent->id,
        ]);
    }

    // ✅ Called after payment succeeds — saves order
    public function success(Request $request)
    {
        $request->validate(['payment_intent_id' => 'required|string']);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
        } catch (\Exception $e) {
            return redirect()->route('user.cart.index')
                ->with('error', 'Invalid payment.');
        }

        // ✅ Verify ownership
        if ($paymentIntent->metadata->user_id != Auth::id()) {
            abort(403);
        }

        // ✅ Verify payment succeeded
        if ($paymentIntent->status !== 'succeeded') {
            return redirect()->route('user.cart.index')
                ->with('error', 'Payment not completed.');
        }

        // ✅ Prevent duplicate orders
        $existingOrder = Order::where('stripe_session_id', $paymentIntent->id)->first();
        if ($existingOrder) {
            return redirect()->route('user.order.show', $existingOrder->id);
        }

        $productIds = explode(',', $paymentIntent->metadata->product_ids);
        $cartItems  = CartItem::where('user_id', Auth::id())
            ->whereIn('product_id', $productIds)
            ->with('product')
            ->get();

        $total = $paymentIntent->amount / 100;

        $order = DB::transaction(function () use ($cartItems, $total, $paymentIntent) {
            $order = Order::create([
                'user_id'           => Auth::id(),
                'payment_method'    => 'stripe',
                'stripe_session_id' => $paymentIntent->id,
                'status'            => 'paid',
                'total_amount'      => $total,
                'currency'          => 'INR',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->product->price * $item->quantity,
                ]);
                $item->product->decrement('stock', $item->quantity);
            }

            CartItem::where('user_id', Auth::id())->delete();
            return $order;
        });

        return redirect()->route('user.order.show', $order->id);
    }

    public function orderShow(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('Items');

        return view('user.checkout-success', [
            'session' => null,
            'order'   => $order,
        ]);
    }

    public function cancel()
    {
        return redirect()->route('user.cart.index')
            ->with('error', 'Payment cancelled.');
    }
}