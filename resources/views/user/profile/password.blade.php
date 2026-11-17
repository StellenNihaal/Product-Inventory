<x-user-layout>
    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <a href="{{ route('user.profile.edit') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Back to
                Profile</a>
            <h1 class="text-xl font-semibold text-gray-800 mt-2">Change Password</h1>
            <p class="text-sm text-gray-400 mt-0.5">Make sure to use a strong password</p>
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-600 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form action="{{ route('user.profile.password.update') }}" method="POST" class="space-y-4" id="passwordForm">
                @csrf
                @method('PATCH')

                {{-- Current Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" id="current_password"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    @error('current_password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password" id="new_password"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="bg-green-500 text-white px-6 py-2 rounded-lg text-sm hover:bg-green-600 transition">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const form = document.getElementById('passwordForm');
            if (!form) return;

            function showError(input, message) {
                clearError(input);
                input.classList.add('border-red-400');
                input.classList.remove('border-gray-200');
                const error = document.createElement('p');
                error.className = 'text-red-400 text-xs mt-1 js-error';
                error.textContent = message;
                input.parentNode.appendChild(error);
            }

            function clearError(input) {
                input.classList.remove('border-red-400');
                input.classList.add('border-gray-200');
                const existing = input.parentNode.querySelector('.js-error');
                if (existing) existing.remove();
            }

            form.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', () => clearError(input));
            });

            form.addEventListener('submit', function(e) {
                let valid = true;

                const current = document.getElementById('current_password');
                const password = document.getElementById('new_password');
                const confirm = document.getElementById('password_confirmation');

                if (!current.value.trim()) {
                    showError(current, 'Current password is required.');
                    valid = false;
                }

                if (!password.value.trim()) {
                    showError(password, 'New password is required.');
                    valid = false;
                } else if (password.value.length < 8) {
                    showError(password, 'Password must be at least 8 characters.');
                    valid = false;
                } else if (password.value === current.value) {
                    showError(password, 'New password must be different from current password.');
                    valid = false;
                }

                if (!confirm.value.trim()) {
                    showError(confirm, 'Please confirm your new password.');
                    valid = false;
                } else if (confirm.value !== password.value) {
                    showError(confirm, 'Passwords do not match.');
                    valid = false;
                }

                if (!valid) e.preventDefault();
            });
        })();
    </script>

</x-user-layout>
