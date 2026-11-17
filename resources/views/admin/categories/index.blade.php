<x-admin-layout>
    <div class="max-w-5xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Categories</h1>
                <p class="text-sm text-gray-400 mt-0.5">Manage product categories</p>
            </div>
            <a href="{{ route('admin.categories.create') }}"
               class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                + Add Category
            </a>
        </div>

        {{-- Search --}}
        <form id="searchForm" method="GET" action="{{ route('admin.categories.index') }}"
              class="bg-white border border-gray-200 rounded-lg p-4 mb-5 flex gap-3">
            <input type="text" name="search" id="searchInput" placeholder="Search categories..."
                   value="{{ request('search') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-56">
            <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                Search
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('admin.categories.index') }}"
                   class="px-4 py-2 rounded-lg text-sm border border-gray-200 text-gray-500 hover:bg-gray-50">
                    Reset
                </a>
            @endif
        </form>

        @if(session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Name</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Products</th>
                        <th class="px-4 py-3 text-left text-gray-500 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr id="category-row-{{ $category->id }}" class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $category->name }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.categories.products', $category) }}"
                               class="bg-blue-50 text-blue-500 px-3 py-1 rounded-full text-xs hover:bg-blue-100 transition">
                                {{ $category->products_count }} products →
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                   class="bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-xs hover:bg-yellow-100 transition">
                                    Edit
                                </a>
                                <button onclick="deleteCategory({{ $category->id }}, '{{ route('admin.categories.destroy', $category) }}')"
                                        class="bg-red-50 text-red-400 px-3 py-1 rounded-lg text-xs hover:bg-red-100 transition">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-400 text-sm">
                            No categories found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <script>
    (function () {
        const searchInput = document.getElementById('searchInput');
        const form        = document.getElementById('searchForm');
        let timer;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const cursorPos = this.selectionStart;
                clearTimeout(timer);
                timer = setTimeout(() => {
                    const params = new URLSearchParams(new FormData(form));
                    loadPage(`{{ route('admin.categories.index') }}?${params.toString()}`)
                    .then(() => {
                        const newInput = document.getElementById('searchInput');
                        if (newInput) {
                            newInput.focus();
                            newInput.setSelectionRange(cursorPos, cursorPos);
                        }
                    });
                }, 350);
            });
        }
    })();

    function deleteCategory(id, url) {
        if (!confirm('Delete this category?')) return;

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
            document.getElementById(`category-row-${id}`)?.remove();
            const existing = document.querySelector('.bg-green-50');
            if (existing) {
                existing.textContent = data.message;
            } else {
                const div = document.createElement('div');
                div.className = 'bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm';
                div.textContent = data.message;
                document.querySelector('.max-w-5xl').insertBefore(div, document.querySelector('.bg-white.border'));
            }
        });
    }
    </script>

</x-admin-layout>