<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                {{ __('Don\'t have an account? Register') }}
            </a>
        </div>
    </form>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[data-validate]');
    if (!form) return;

    function showError(input, message) {
        clearError(input);
        input.classList.add('border-red-500');
        const error = document.createElement('p');
        error.className = 'text-red-500 text-xs mt-1 js-error';
        error.textContent = message;
        input.parentNode.appendChild(error);
    }

    function clearError(input) {
        input.classList.remove('border-red-500');
        const existing = input.parentNode.querySelector('.js-error');
        if (existing) existing.remove();
    }

    // Clear on type
    form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => clearError(input));
    });

    form.addEventListener('submit', function (e) {
        let valid = true;

        const email = document.getElementById('email');
        const password = document.getElementById('password');

        // Email validation
        if (!email.value.trim()) {
            showError(email, 'Email is required.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            showError(email, 'Please enter a valid email address.');
            valid = false;
        }

        // Password validation
        if (!password.value.trim()) {
            showError(password, 'Password is required.');
            valid = false;
        } else if (password.value.length < 8) {
            showError(password, 'Password must be at least 8 characters.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
});
</script>
</x-guest-layout>
