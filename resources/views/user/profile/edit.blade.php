<x-user-layout>
    <style>
        .ts-wrapper .ts-control {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            box-shadow: none !important;
            font-size: 0.875rem !important;
            min-height: unset !important;
        }

        .ts-wrapper.focus .ts-control {
            border-color: #86efac !important;
            ring: 2px #86efac !important;
            outline: none !important;
        }

        .ts-wrapper .ts-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important;
            font-size: 0.875rem !important;
        }

        .ts-wrapper {
            padding: 0 !important;
        }
    </style>

    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-xl font-semibold text-gray-800">My Profile</h1>
            <p class="text-sm text-gray-400 mt-0.5">Update your name, email and photo</p>
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4"
                data-validate>
                @csrf
                @method('PATCH')

                {{-- Profile Photo --}}
                <div class="flex items-center gap-4">
                    @if (auth()->user()->profile_photo)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}"
                            class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                    @else
                        <div
                            class="w-16 h-16 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-xl">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <input type="file" name="profile_photo" accept="image/*" data-image
                            class="text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-600 hover:file:bg-green-100">
                        @error('profile_photo')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                        data-required="Name is required." data-minlength="2"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                        data-required="Email is required." data-email="Please enter a valid email address."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Country --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-0">Country</label>
                    <select name="country" id="countrySelect" class="w-full text-sm">
                        <option value="">Loading countries...</option>
                    </select>
                    @error('country')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- State --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <select name="state" id="stateSelect" class="w-full text-sm">
                        <option value="">Select country first</option>
                    </select>
                    @error('state')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-green-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>

    <script>
        (function() {
            const savedCountry = "{{ old('country', auth()->user()->country ?? '') }}";
            const savedState = "{{ old('state', auth()->user()->state ?? '') }}";

            let tomCountry = null;
            let tomState = null;

            // ✅ Init Tom Select on country
            tomCountry = new TomSelect('#countrySelect', {
                placeholder: 'Select Country',
                maxOptions: false,
                render: {
                    option: (data, escape) => `
            <div style="display:flex; align-items:center; gap:8px; padding:4px 8px;">
                <img src="https://flagcdn.com/24x18/${escape(data.code || '')}.png"
                     onerror="this.style.display='none'" width="24" height="18"
                     style="flex-shrink:0;">
                <span>${escape(data.text)}</span>
            </div>`,
                    item: (data, escape) => `
            <div style="display:inline-flex; align-items:center; gap:6px;">
                <img src="https://flagcdn.com/24x18/${escape(data.code || '')}.png"
                     onerror="this.style.display='none'" width="20" height="15"
                     style="flex-shrink:0; vertical-align:middle;">
                <span style="line-height:1;">${escape(data.text)}</span>
            </div>`
                },
                onChange: function(value) {
                    loadStates(value, '');
                }
            });

            //  Init Tom Select on state
            tomState = new TomSelect('#stateSelect', {
                placeholder: 'Select State',
            });

            // ✅ Load countries
            fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
                .then(res => res.json())
                .then(data => {
                    data.sort((a, b) => a.name.common.localeCompare(b.name.common));

                    tomCountry.clearOptions();
                    tomCountry.addOption({
                        value: '',
                        text: 'Select Country',
                        code: ''
                    });

                    data.forEach(c => {
                        tomCountry.addOption({
                            value: c.name.common,
                            text: c.name.common,
                            code: c.cca2.toLowerCase()
                        });
                    });

                    tomCountry.refreshOptions(false);

                    if (savedCountry) {
                        tomCountry.setValue(savedCountry);
                        loadStates(savedCountry, savedState);
                    }
                })
                .catch(() => {
                    tomCountry.clearOptions();
                    tomCountry.addOption({
                        value: '',
                        text: 'Failed to load countries',
                        code: ''
                    });
                });

            function loadStates(country, preselectState) {
                tomState.clear();
                tomState.clearOptions();
                tomState.addOption({
                    value: '',
                    text: 'Loading states...'
                });
                tomState.refreshOptions(false);

                if (!country) {
                    tomState.clearOptions();
                    tomState.addOption({
                        value: '',
                        text: 'Select country first'
                    });
                    return;
                }

                fetch('https://countriesnow.space/api/v0.1/countries/states', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            country
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        tomState.clearOptions();
                        tomState.addOption({
                            value: '',
                            text: 'Select State'
                        });

                        if (data.error || !data.data?.states?.length) {
                            tomState.addOption({
                                value: '',
                                text: 'No states available'
                            });
                            return;
                        }

                        data.data.states.forEach(s => {
                            tomState.addOption({
                                value: s.name,
                                text: s.name
                            });
                        });

                        tomState.refreshOptions(false);
                        if (preselectState) tomState.setValue(preselectState);
                    })
                    .catch(() => {
                        tomState.clearOptions();
                        tomState.addOption({
                            value: '',
                            text: 'Failed to load states'
                        });
                    });
            }
        })();
    </script>

</x-user-layout>
