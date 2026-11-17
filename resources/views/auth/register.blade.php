<x-guest-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <form method="POST" action="{{ route('register') }}" data-validate>
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Country -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
            <select name="country" id="countrySelect" class="w-full text-sm">
                <option value="">Loading countries...</option>
            </select>
            @error('country')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- State -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
            <select name="state" id="stateSelect" class="w-full text-sm">
                <option value="">Select country first</option>
            </select>
            @error('state')
                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        (function() {
            const savedCountry = "{{ old('country', '') }}";
            const savedState = "{{ old('state', '') }}";

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

            // ✅ Init Tom Select on state
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
                    tomCountry.addOption({
                        value: '',
                        text: 'Failed to load countries'
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

</x-guest-layout>
