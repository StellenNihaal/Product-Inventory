<x-admin-layout>
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Products</h1>
                <p class="text-sm text-gray-400 mt-0.5">Manage your product inventory</p>
            </div>
            <a href="{{ route('admin.products.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                + Add Product
            </a>
        </div>

        {{-- Search & Filter --}}
        <form id="filterForm" method="GET" action="{{ route('admin.products.index') }}"
              class="bg-white border border-gray-200 rounded-lg p-4 mb-5 flex flex-wrap gap-3">

            <input type="text" name="search" placeholder="Search by name..."
                   value="{{ request('search') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-48">

            <select name="category_id"
                    class="border border-gray-200 rounded-lg px-7 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="brand_id"
                    class="border border-gray-200 rounded-lg px-7 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>

            <input type="number" name="min_price" placeholder="Min ₹"
                   value="{{ request('min_price') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-24">

            <input type="number" name="max_price" placeholder="Max ₹"
                   value="{{ request('max_price') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-24">

            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'category_id', 'brand_id', 'min_price', 'max_price']))
                <a href="{{ route('admin.products.index') }}"
                   class="px-4 py-2 rounded-lg text-sm border border-gray-200 text-gray-500 hover:bg-gray-50">
                    Reset
                </a>
            @endif
        </form>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm" id="successMsg">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Image</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Name</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Category</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Brand</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Price</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Stock</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @forelse($products as $product)
                    <tr id="product-row-{{ $product->id }}" class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     class="w-10 h-10 object-cover rounded-lg border border-gray-100">
                            @else
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs">N/A</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                        <td class="px-4 py-3">
                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full text-xs">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full text-xs">
                                {{ $product->brand->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-800 font-medium">₹{{ number_format($product->price, 2) }}</td>
                        <td class="px-4 py-3">
                            @if($product->stock > 0)
                                <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded-full text-xs font-medium">
                                    {{ $product->stock }}
                                </span>
                            @else
                                <span class="bg-red-50 text-red-400 px-2 py-0.5 rounded-full text-xs font-medium">
                                    Out of stock
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-xs hover:bg-yellow-100 transition">
                                    Edit
                                </a>
                                {{-- ✅ AJAX delete button --}}
                                <button onclick="deleteProduct({{ $product->id }}, '{{ route('admin.products.destroy', $product) }}')"
                                        class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-xs hover:bg-red-100 transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-gray-400 text-sm">
                            No products found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <script>
    (function () {
        const form    = document.getElementById('filterForm');
        const search  = form.querySelector('input[name="search"]');
        let timer;

        // ✅ Live search
        search.addEventListener('input', function () {
            const cursorPos = this.selectionStart;
            clearTimeout(timer);
            timer = setTimeout(() => {
                const params = new URLSearchParams(new FormData(form));
                loadPage(`{{ route('admin.products.index') }}?${params.toString()}`)
                .then(() => {
                    const newSearch = document.querySelector('input[name="search"]');
                    if (newSearch) { newSearch.focus(); newSearch.setSelectionRange(cursorPos, cursorPos); }
                });
            }, 400);
        });

        // ✅ Live category/brand filter
        form.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function () {
                const params = new URLSearchParams(new FormData(form));
                loadPage(`{{ route('admin.products.index') }}?${params.toString()}`);
            });
        });

        // ✅ Live price filter
        form.querySelectorAll('input[name="min_price"], input[name="max_price"]').forEach(input => {
            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const params = new URLSearchParams(new FormData(form));
                    loadPage(`{{ route('admin.products.index') }}?${params.toString()}`);
                }, 500);
            });
        });
    })();

    // ✅ AJAX delete
    function deleteProduct(id, url) {
        if (!confirm('Delete this product?')) return;

        const form = new FormData();
        form.append('_token', CSRF);
        form.append('_method', 'DELETE');

        fetch(url, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: form
        })
        .then(res => res.json())
        .then(data => {
            // Remove row from table
            document.getElementById(`product-row-${id}`)?.remove();

            // Show success message
            const msg = document.getElementById('successMsg');
            if (msg) {
                msg.textContent = data.message;
            } else {
                const div = document.createElement('div');
                div.id = 'successMsg';
                div.className = 'bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm';
                div.textContent = data.message;
                document.querySelector('.max-w-7xl').insertBefore(div, document.querySelector('.bg-white.border'));
            }
        });
    }
    </script>

</x-admin-layout>