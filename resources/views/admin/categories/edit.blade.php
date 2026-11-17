<x-admin-layout>
    <div class="max-w-lg mx-auto">

        <div class="mb-6">
            <a href="{{ route('admin.categories.index') }}"
               class="text-gray-400 hover:text-gray-600 text-sm">← Back to Categories</a>
            <h1 class="text-xl font-semibold text-gray-800 mt-2">Edit Category</h1>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            {{-- ✅ Fixed route and method --}}
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4" data-validate>
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                    {{-- ✅ Pre-fill with existing category name --}}
                    <input type="text" name="name" value="{{ old('name', $category->name) }}"
                           data-required="Category name is required."
                           data-minlength="2"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                        Update Category
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                       class="px-6 py-2 rounded-lg text-sm border border-gray-200 text-gray-500 hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>