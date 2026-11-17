<x-user-layout>
    <div class="max-w-7xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Product Catalog</h1>

        {{-- Search & Filter Form --}}
        <form method="GET" action="{{ route('products.index') }}"
            class="bg-gray-50 p-4 rounded mb-6 grid grid-cols-2 md:grid-cols-5 gap-3">

            <input type="text" name="search" placeholder="Search by name..." value="{{ request('search') }}"
                class="border rounded px-3 py-2 col-span-2 md:col-span-1">

            <select name="category_id" class="border rounded px-3 py-2">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="brand_id" class="border rounded px-3 py-2">
                <option value="">All Brands</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="number" name="min_price" placeholder="Min ₹" value="{{ request('min_price') }}"
                    class="border rounded px-3 py-2 w-full">
                <input type="number" name="max_price" placeholder="Max ₹" value="{{ request('max_price') }}"
                    class="border rounded px-3 py-2 w-full">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                    Filter
                </button>
                <a href="{{ route('products.index') }}"
                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 w-full text-center">
                    Reset
                </a>
            </div>
        </form>

        {{-- Product Grid --}}
        @if ($products->isEmpty())
            <p class="text-gray-500">No products found.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white border rounded-lg overflow-hidden hover:shadow-md transition">

                        {{-- Clickable area → product detail --}}
                        <a href="{{ route('products.show', $product) }}">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                    class="w-full h-48 object-contain p-2 bg-white">
                            @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400">
                                    No Image
                                </div>
                            @endif
                            <div class="px-3 pt-3">
                                <h2 class="font-semibold text-sm">{{ $product->name }}</h2>
                                <p class="text-xs text-gray-500">{{ $product->category->name }} ·
                                    {{ $product->brand->name }}</p>
                                <p class="text-blue-600 font-bold mt-1">₹{{ $product->price }}</p>
                                <p class="text-xs text-gray-400">Stock: {{ $product->stock }}</p>
                            </div>
                        </a>

                        {{-- Cart control — outside <a> so clicks don't navigate --}}
                        <div class="px-3 pb-3 mt-2">
                            <div class="cart-control" data-product-id="{{ $product->id }}"
                                data-url="{{ route('user.cart.add', $product) }}"
                                data-decrement-url="{{ route('user.cart.decrement', $product) }}">

                                @if (isset($cartItems[$product->id]))
                                    <div class="flex items-center gap-1">
                                        <button onclick="changeQty(this, -1)"
                                            class="w-7 h-7 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold flex items-center justify-center">
                                            −
                                        </button>
                                        <span class="qty-display text-sm font-semibold text-gray-800 w-5 text-center">
                                            {{ $cartItems[$product->id] }}
                                        </span>
                                        <button onclick="changeQty(this, 1)"
                                            class="w-7 h-7 rounded bg-green-500 hover:bg-green-600 text-white font-bold flex items-center justify-center">
                                            +
                                        </button>
                                    </div>
                                @else
                                    <button
                                        class="add-to-cart-btn w-full bg-green-500 text-white text-xs py-1.5 rounded-lg hover:bg-green-600 transition">
                                        + Cart
                                    </button>
                                @endif

                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $products->links() }}</div>
        @endif
    </div>

    <script>
        (function() {
            document.querySelectorAll('.add-to-cart-btn').forEach(btn => attachAddToCart(btn));

            const searchInput = document.querySelector('input[name="search"]');
            let searchTimer;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const cursorPos = this.selectionStart; // ✅ save cursor position
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        const params = new URLSearchParams(new FormData(this.closest('form')));
                        loadPage(`{{ route('products.index') }}?${params.toString()}`)
                            .then(() => {
                                // ✅ restore focus and cursor after page updates
                                const newInput = document.querySelector('input[name="search"]');
                                if (newInput) {
                                    newInput.focus();
                                    newInput.setSelectionRange(cursorPos, cursorPos);
                                }
                            });
                    }, 350);
                });
            }

            document.querySelectorAll('select[name="category_id"], select[name="brand_id"]').forEach(select => {
                select.addEventListener('change', function() {
                    const params = new URLSearchParams(new FormData(this.closest('form')));
                    loadPage(`{{ route('products.index') }}?${params.toString()}`);
                });
            });

            document.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        const params = new URLSearchParams(new FormData(this.closest('form')));
                        loadPage(`{{ route('products.index') }}?${params.toString()}`);
                    }, 500);
                });
            });
        })();
    </script>

</x-user-layout>
