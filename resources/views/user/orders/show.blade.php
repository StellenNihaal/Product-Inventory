<x-user-layout>
    <div class="max-w-2xl mx-auto">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('user.orders.index') }}" class="text-gray-400 hover:text-gray-600">←</a>
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Order #{{ $order->id }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-4">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h2 class="font-medium text-gray-800">Items</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Qty</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Price</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-gray-500">₹{{ number_format($item->price, 2) }}</td>
                        <td class="px-4 py-3 font-semibold">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-4 py-3 flex justify-between border-t border-gray-200">
                <span class="font-semibold text-gray-800">Total</span>
                <span class="font-bold text-green-600 text-lg">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        {{-- Order Info --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 text-sm">
            <div class="flex justify-between py-1.5">
                <span class="text-gray-500">Order ID</span>
                <span class="font-medium">#{{ $order->id }}</span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-gray-500">Status</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $order->status === 'paid' ? 'bg-green-50 text-green-600' :
                      ($order->status === 'cancelled' ? 'bg-red-50 text-red-400' : 'bg-yellow-50 text-yellow-600') }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-gray-500">Payment</span>
                <span class="font-medium text-gray-800">Stripe</span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-gray-500">Currency</span>
                <span class="font-medium text-gray-800">{{ $order->currency }}</span>
            </div>
        </div>
    </div>
</x-user-layout>