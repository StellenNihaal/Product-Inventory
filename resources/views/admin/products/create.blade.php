<x-admin-layout>
    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('admin.products.index') }}"
               class="text-gray-400 hover:text-gray-600 text-sm">← Back to Products</a>
            <h1 class="text-xl font-semibold text-gray-800 mt-2">Add New Product</h1>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form action="{{ route('admin.products.store') }}" method="POST"
      enctype="multipart/form-data" class="space-y-4" data-validate>
    @csrf

    <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   data-required="Product name is required."
                   data-minlength="3"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category_id"
                    data-required="Please select a category."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
            <select name="brand_id"
                    data-required="Please select a brand."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">Select Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Price (₹)</label>
            <input type="number" name="price" step="0.01" value="{{ old('price') }}"
                   data-required="Price is required."
                   data-numeric
                   data-min="0"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
            <input type="number" name="stock" value="{{ old('stock') }}"
                   data-required="Stock quantity is required."
                   data-numeric
                   data-min="0"
                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
            @error('stock') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
            <input type="file" name="image" accept="image/*"
                   data-image
                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
            @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit"
                class="bg-blue-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
            Save Product
        </button>
        <a href="{{ route('admin.products.index') }}"
           class="px-6 py-2 rounded-lg text-sm border border-gray-200 text-gray-500 hover:bg-gray-50">
            Cancel
        </a>
    </div>
</form>

        </div>
    </div>
</x-admin-layout>