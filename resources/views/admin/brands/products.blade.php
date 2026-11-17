<x-admin-layout>
    <div class="max-w-5xl mx-auto">

        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.brands.index') }}"
               class="text-gray-400 hover:text-gray-600 text-sm">← Brands</a>
            <span class="text-gray-300">/</span>
            <h1 class="text-xl font-semibold text-gray-800">{{ $brand->name }}</h1>
            <span class="bg-purple-50 text-purple-500 text-xs px-2 py-1 rounded-full">
                {{ $products->total() }} products
            </span>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            @if($products->isEmpty())
                <div class="px-4 py-10 text-center text-gray-400 text-sm">
                    No products under this brand yet.
                </div>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">#</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Name</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Category</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Price</th>
                            <th class="px-4 py-3 text-left text-gray-500 font-medium">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full text-xs">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">₹{{ number_format($product->price, 2) }}</td>
                            <td class="px-4 py-3">
                                @if($product->stock > 0)
                                    <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded-full text-xs">
                                        {{ $product->stock }}
                                    </span>
                                @else
                                    <span class="bg-red-50 text-red-400 px-2 py-0.5 rounded-full text-xs">
                                        Out of stock
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>