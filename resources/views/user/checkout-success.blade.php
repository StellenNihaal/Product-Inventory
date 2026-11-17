<x-user-layout>
    <div class="max-w-lg mx-auto py-10 text-center">

        {{-- Success Icon --}}
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-800">Payment Successful!</h1>

        {{-- ✅ Only show order ID if order exists --}}
        @if($order)
            <p class="text-gray-500 mt-1">Order #{{ $order->id }} has been placed.</p>
        @else
            <p class="text-gray-500 mt-1">Your payment has been processed successfully.</p>
        @endif

        {{-- Order Items --}}
        @if($order && $order->Items->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-4 mt-6 text-left">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Qty</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->Items as $item)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-3 text-gray-800">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-gray-800">₹{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-4 py-3 flex justify-between border-t border-gray-100">
                <span class="font-semibold text-gray-800">Total</span>
                <span class="font-bold text-green-600">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
        @endif

        {{-- Payment Method Badge --}}
        @if($order)
        <div class="inline-block bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full mb-6">
            Paid via {{ strtoupper($order->payment_method) }}
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex gap-3 justify-center mt-6">
            <a href="{{ route('products.index') }}"
               class="bg-green-500 text-white px-6 py-2.5 rounded-lg text-sm hover:bg-green-600 transition">
                Continue Shopping
            </a>
            <a href="{{ route('user.dashboard') }}"
               class="border border-gray-200 text-gray-600 px-6 py-2.5 rounded-lg text-sm hover:bg-gray-50 transition">
                Go to Dashboard
            </a>
        </div>
    </div>
</x-user-layout>