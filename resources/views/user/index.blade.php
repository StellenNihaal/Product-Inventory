<x-user-layout>
    <div class="max-w-4xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">My Cart</h1>
                <p class="text-sm text-gray-400 mt-0.5">Review your selected items</p>
            </div>
            @if ($cartItems->count() > 0)
                <form action="{{ route('user.cart.clear') }}" method="POST"
                    onsubmit="return confirm('Clear entire cart?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-400 text-sm hover:underline">Clear Cart</button>
                </form>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <div class="bg-white border border-gray-200 rounded-lg p-10 text-center">
                <p class="text-gray-400 text-sm">Your cart is empty.</p>
                <a href="{{ route('products.index') }}"
                    class="inline-block mt-3 bg-green-500 text-white px-5 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                    Browse Products
                </a>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden mb-4">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Product</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Price</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Quantity</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Subtotal</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cartItems as $item)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                class="w-10 h-10 object-cover rounded-lg border border-gray-100">
                                        @else
                                            <div
                                                class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                                N/A</div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $item->product->category->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-800">₹{{ number_format($item->product->price, 2) }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('user.cart.update', $item) }}" method="POST"
                                        class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item->quantity }}"
                                            min="1" max="{{ $item->product->stock }}"
                                            class="w-16 border border-gray-200 rounded-lg px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-green-300">
                                        <button class="text-xs text-green-500 hover:underline">Update</button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('user.cart.remove', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-400 hover:underline text-xs">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-400">Total Items: {{ $cartItems->sum('quantity') }}</p>
                    <p class="text-xl font-semibold text-gray-800 mt-1">
                        Total: ₹{{ number_format($total, 2) }}
                    </p>
                </div>
                <a href="{{ route('products.index') }}"
                    class="bg-green-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                    Continue Shopping
                </a>
            </div>
        @endif
    </div>
</x-user-layout>
