<x-admin-layout>
    <div class="max-w-4xl mx-auto">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-xl font-semibold text-gray-800">Landing Page CMS</h1>
                <p class="text-sm text-gray-400 mt-0.5">Edit all landing page content from here</p>
            </div>
            <a href="{{ route('landing') }}?preview=1" target="_blank"
                class="text-sm text-blue-500 hover:underline">Preview Landing Page →</a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.landing.update') }}" method="POST">
            @csrf
            @method('PATCH')

            @foreach ($contents as $section => $fields)
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <h2
                        class="text-sm font-semibold text-blue-600 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">
                        {{ ucfirst($section) }}
                    </h2>
                    <div class="space-y-4">
                        @foreach ($fields as $field)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $field->label }}
                                </label>
                                @if (str_contains(strtolower($field->label), 'subtitle') || str_contains(strtolower($field->label), 'description'))
                                    <textarea name="{{ $field->key }}" rows="3"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">{{ $field->value }}</textarea>
                                @else
                                    <input type="text" name="{{ $field->key }}" value="{{ $field->value }}"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 -mx-6 rounded-b-lg">
                <button type="submit"
                    class="bg-blue-500 text-white px-8 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
