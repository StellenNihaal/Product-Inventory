<x-user-layout>
    <div class="max-w-4xl mx-auto p-6">
        <a href="{{ route('products.index') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">← Back to
            Catalog</a>

        <div class="bg-white border rounded-lg overflow-hidden flex flex-col md:flex-row">
            {{-- Image --}}
            <div class="md:w-1/2">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-48 object-contain p-2 bg-white">
                @else
                    <div class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-400">
                        No Image
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="md:w-1/2 p-6 space-y-3">
                <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
                <p class="text-gray-500 text-sm">{{ $product->category->name }} · {{ $product->brand->name }}</p>
                <p class="text-3xl font-bold text-blue-600">₹{{ $product->price }}</p>
                <p class="text-sm text-gray-600">{{ $product->description ?? 'No description available.' }}</p>
                <div class="pt-2">
                    <span class="text-sm font-medium">Stock: </span>
                    <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }} text-sm font-semibold">
                        {{ $product->stock > 0 ? $product->stock . ' available' : 'Out of Stock' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>
