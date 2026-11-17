<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // ✅ Admin — list all orders
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $query = Order::with('user', 'items');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'orders'  => $orders->map(fn($order) => [
                'id'             => $order->id,
                'status'         => $order->status,
                'payment_method' => $order->payment_method,
                'total_amount'   => $order->total_amount,
                'currency'       => $order->currency,
                'created_at'     => $order->created_at->format('d M Y, h:i A'),
                'user'           => [
                    'id'    => $order->user->id,
                    'name'  => $order->user->name,
                    'email' => $order->user->email,
                ],
                'items_count'    => $order->items->count(),
            ]),
            'pagination' => [
                'total'        => $orders->total(),
                'per_page'     => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    // ✅ Admin — get single order details
    public function show(Order $order)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $order->load('user', 'items.product');

        return response()->json([
            'success' => true,
            'order'   => [
                'id'             => $order->id,
                'status'         => $order->status,
                'payment_method' => $order->payment_method,
                'total_amount'   => $order->total_amount,
                'currency'       => $order->currency,
                'created_at'     => $order->created_at->format('d M Y, h:i A'),
                'user'           => [
                    'id'    => $order->user->id,
                    'name'  => $order->user->name,
                    'email' => $order->user->email,
                ],
                'items'          => $order->items->map(fn($item) => [
                    'id'           => $item->id,
                    'product_name' => $item->product_name,
                    'price'        => $item->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->subtotal,
                    'product'      => $item->product ? [
                        'id'    => $item->product->id,
                        'name'  => $item->product->name,
                        'image' => $item->product->image
                                        ? asset('storage/' . $item->product->image)
                                        : null,
                    ] : null,
                ]),
            ],
        ]);
    }

    // ✅ Admin — update order status
    public function updateStatus(Request $request, Order $order)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
        ], [
            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be pending, paid or cancelled.',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully.',
            'order'   => [
                'id'     => $order->id,
                'status' => $order->status,
            ],
        ]);
    }

    // ✅ User — get their own orders
    public function userOrders(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
                    ->with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'orders'  => $orders->map(fn($order) => [
                'id'             => $order->id,
                'status'         => $order->status,
                'payment_method' => $order->payment_method,
                'total_amount'   => $order->total_amount,
                'currency'       => $order->currency,
                'created_at'     => $order->created_at->format('d M Y, h:i A'),
                'items_count'    => $order->items->count(),
            ]),
            'pagination' => [
                'total'        => $orders->total(),
                'per_page'     => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    // ✅ User — get single order detail
    public function userOrderShow(Order $order)
    {
        // Ownership check
        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'order'   => [
                'id'             => $order->id,
                'status'         => $order->status,
                'payment_method' => $order->payment_method,
                'total_amount'   => $order->total_amount,
                'currency'       => $order->currency,
                'created_at'     => $order->created_at->format('d M Y, h:i A'),
                'items'          => $order->items->map(fn($item) => [
                    'id'           => $item->id,
                    'product_name' => $item->product_name,
                    'price'        => $item->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->subtotal,
                    'product'      => $item->product ? [
                        'id'    => $item->product->id,
                        'image' => $item->product->image
                                        ? asset('storage/' . $item->product->image)
                                        : null,
                    ] : null,
                ]),
            ],
        ]);
    }
}