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

        @if ($cartItems->isEmpty())
            <div class="bg-white border border-gray-200 rounded-lg p-10 text-center">
                <p class="text-4xl mb-3">🛒</p>
                <p class="text-gray-500 font-medium">Your cart is empty</p>
                <a href="{{ route('products.index') }}"
                    class="inline-block mt-4 bg-green-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-600 transition">
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
                            {{-- ✅ id on every row --}}
                            <tr id="row-{{ $item->id }}" class="border-b border-gray-100 hover:bg-gray-50">

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                class="w-10 h-10 object-contain rounded-lg border border-gray-100">
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

                                <td class="px-4 py-3 text-gray-600">
                                    ₹{{ number_format($item->product->price, 2) }}
                                </td>

                                {{-- Quantity controls --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button
                                            onclick="cartUpdate({{ $item->id }}, {{ $item->quantity - 1 }}, '{{ route('user.cart.update', $item) }}', '{{ route('user.cart.remove', $item) }}')"
                                            class="w-7 h-7 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold flex items-center justify-center">−</button>
                                        {{-- ✅ id on qty span --}}
                                        <span id="qty-{{ $item->id }}"
                                            class="text-sm font-semibold w-6 text-center">{{ $item->quantity }}</span>
                                        <button
                                            onclick="cartUpdate({{ $item->id }}, {{ $item->quantity + 1 }}, '{{ route('user.cart.update', $item) }}', '{{ route('user.cart.remove', $item) }}')"
                                            class="w-7 h-7 rounded bg-green-500 hover:bg-green-600 text-white font-bold flex items-center justify-center">+</button>
                                    </div>
                                </td>

                                {{-- ✅ id on subtotal cell --}}
                                <td class="px-4 py-3 font-medium text-gray-800" id="subtotal-{{ $item->id }}">
                                    ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                                </td>

                                <td class="px-4 py-3">
                                    <button
                                        onclick="cartRemove({{ $item->id }}, '{{ route('user.cart.remove', $item) }}')"
                                        class="text-red-400 hover:underline text-xs">Remove</button>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex justify-between items-center">
                    <div class="space-y-1">
                        <p class="text-sm text-gray-400">
                            Total Items: <span id="total-items"
                                class="text-gray-700 font-medium">{{ $cartItems->sum('quantity') }}</span>
                        </p>
                        <p class="text-sm text-gray-400">
                            Total Products: <span class="text-gray-700 font-medium">{{ $cartItems->count() }}</span>
                        </p>
                        <p class="text-xl font-semibold text-gray-800 pt-1">
                            Total: <span class="text-green-600">₹<span
                                    id="cart-total">{{ number_format($total, 2) }}</span></span>
                        </p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('products.index') }}"
                            class="px-5 py-2 rounded-lg text-sm border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                            Continue Shopping
                        </a>
                        <a href="{{ route('user.checkout') }}"
                            class="bg-green-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                            Checkout
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function cartUpdate(itemId, newQty, updateUrl, removeUrl) {
            if (newQty <= 0) {
                cartRemove(itemId, removeUrl);
                return;
            }

            const form = new FormData();
            form.append('_token', CSRF);
            form.append('_method', 'PATCH');
            form.append('quantity', newQty);

            fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: form
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById(`qty-${itemId}`).textContent = newQty;
                    document.getElementById(`subtotal-${itemId}`).textContent = '₹' + data.subtotal;
                    document.getElementById('cart-total').textContent = data.total;
                    document.getElementById('total-items').textContent = data.count;
                    updateGlobalCount(data.count);

                    // ✅ Update onclick attributes for next click
                    const row = document.getElementById(`row-${itemId}`);
                    const btns = row.querySelectorAll('button');
                    btns[0].setAttribute('onclick',
                        `cartUpdate(${itemId}, ${newQty - 1}, '${updateUrl}', '${removeUrl}')`);
                    btns[1].setAttribute('onclick',
                        `cartUpdate(${itemId}, ${newQty + 1}, '${updateUrl}', '${removeUrl}')`);
                });
        }

        function cartRemove(itemId, removeUrl) {
            const form = new FormData();
            form.append('_token', CSRF);
            form.append('_method', 'DELETE');

            fetch(removeUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: form
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById(`row-${itemId}`)?.remove();
                    document.getElementById('cart-total').textContent = data.total;
                    document.getElementById('total-items').textContent = data.count;
                    updateGlobalCount(data.count);
                });
        }
    </script>
</x-user-layout>
