<x-user-layout>
    <div class="max-w-4xl mx-auto">

        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">My Orders</h1>
            <p class="text-sm text-gray-400 mt-0.5">Track all your orders</p>
        </div>

        @if ($orders->isEmpty())
            <div
                class="bg-white border border-gray-200 rounded-lg p-12 text-center h-50 flex flex-col items-center justify-center">
                <p class="text-gray-400 text-sm">You haven't placed any orders yet.</p>
                <a href="{{ route('products.index') }}"
                    class="mt-4 inline-block bg-green-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Order</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Items</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Total</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Status</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Date</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                                <td class="px-4 py-3 font-medium text-gray-800">#{{ $order->id }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $order->items->count() }} item(s)</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">
                                    ₹{{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status === 'paid'
                                        ? 'bg-green-50 text-green-600'
                                        : ($order->status === 'cancelled'
                                            ? 'bg-red-50 text-red-400'
                                            : 'bg-yellow-50 text-yellow-600') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('user.order.show', $order) }}"
                                        class="text-blue-500 hover:underline text-xs">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
</x-user-layout>
