<x-admin-layout>
    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">My Profile</h1>
            <p class="text-sm text-gray-400 mt-0.5">Update your name, email and photo</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form action="{{ route('profile.update') }}" method="POST"
                  enctype="multipart/form-data" class="space-y-4" data-validate>
                @csrf
                @method('PATCH')

                {{-- Profile Photo --}}
                <div class="flex items-center gap-4">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                             class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                    @else
                        <div class="w-16 h-16 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <input type="file" name="profile_photo" accept="image/*"
                               data-image  {{-- ✅ validates image type & size --}}
                               class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
                        @error('profile_photo')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                           data-required="Name is required."  {{-- ✅ --}}
                           data-minlength="2"                 {{-- ✅ --}}
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           data-required="Email is required."             {{-- ✅ --}}
                           data-email="Please enter a valid email address." {{-- ✅ --}}
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-blue-600 transition">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>