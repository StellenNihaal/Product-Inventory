<x-user-layout>
    <div class="max-w-6xl mx-auto">

        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">Welcome, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-sm text-gray-400 mt-0.5">Here's what's available for you</p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-5 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <a href="{{ route('user.cart.index') }}">
                    <p class="text-sm text-gray-400">Cart Items</p>
                    <p class="text-2xl font-semibold text-gray-800 mt-1" id="cart-items-count">{{ $cartCount }}</p>
                    <div class="w-1 h-8 bg-yellow-400 rounded float-right -mt-8"></div>
                </a>
            </div>
        </div>

        {{-- Recent Products --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-medium text-gray-800">Recent Products</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-green-500 hover:underline">View all →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                @foreach ($recentProducts as $product)
                    <div class="border border-gray-100 rounded-lg overflow-hidden hover:shadow-sm transition">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                class="w-full h-48 object-contain p-2 bg-white">
                        @else
                            <div class="w-full h-36 bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                                No Image
                            </div>
                        @endif
                        <div class="p-3">
                            <p class="font-medium text-gray-800 text-sm">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $product->category->name }} ·
                                {{ $product->brand->name }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-green-600 font-semibold text-sm">₹{{ number_format($product->price, 2) }}
                                </p>

                                <div class="cart-control" data-product-id="{{ $product->id }}"
                                    data-url="{{ route('user.cart.add', $product) }}"
                                    data-decrement-url="{{ route('user.cart.decrement', $product) }}">

                                    @if (isset($cartItems[$product->id]))
                                        {{-- Already in cart — show qty controls --}}
                                        <div class="flex items-center gap-1">
                                            <button onclick="changeQty(this, -1)"
                                                class="w-6 h-6 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-sm flex items-center justify-center">
                                                −
                                            </button>
                                            <span
                                                class="qty-display text-sm font-semibold text-gray-800 w-4 text-center">
                                                {{ $cartItems[$product->id] }}
                                            </span>
                                            <button onclick="changeQty(this, 1)"
                                                class="w-6 h-6 rounded bg-green-500 hover:bg-green-600 text-white font-bold text-sm flex items-center justify-center">
                                                +
                                            </button>
                                        </div>
                                    @else
                                        {{-- Not in cart --}}
                                        <form action="{{ route('user.cart.add', $product) }}" method="POST">
                                            @csrf
                                            <button
                                                class="add-to-cart-btn bg-green-500 text-white text-xs px-3 py-1 rounded-lg hover:bg-green-600 transition">
                                                + Cart
                                            </button>
                                        </form>
                                    @endif

                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => attachAddToCart(btn));
    </script>

</x-user-layout>
