<x-admin-layout>
    <div class="max-w-6xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Orders</h1>
                <p class="text-sm text-gray-400 mt-0.5">Manage all customer orders</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Order</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Items</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Total</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Status</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-medium text-gray-800">#{{ $order->id }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center text-white text-xs font-medium">
                                    {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $order->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $order->items->count() }} item(s)</td>
                        <td class="px-4 py-3 font-semibold text-gray-800">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status === 'paid'      ? 'bg-green-50 text-green-600'  :
                                  ($order->status === 'cancelled' ? 'bg-red-50 text-red-400'      :
                                                                    'bg-yellow-50 text-yellow-600') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="text-blue-500 hover:underline text-xs">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400 text-sm">
                            No orders yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>