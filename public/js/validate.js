function initValidation() {
    const form = document.querySelector('form[data-validate]');
    if (!form) return;

    function showError(input, message) {
        clearError(input);
        input.classList.add('border-red-400', 'focus:ring-red-300');
        input.classList.remove('border-gray-200', 'focus:ring-blue-300');
        const error = document.createElement('p');
        error.className = 'text-red-400 text-xs mt-1 validate-error';
        error.textContent = message;
        input.parentNode.appendChild(error);
    }

    function clearError(input) {
        input.classList.remove('border-red-400', 'focus:ring-red-300');
        input.classList.add('border-gray-200', 'focus:ring-blue-300');
        const existing = input.parentNode.querySelector('.validate-error');
        if (existing) existing.remove();
    }

    form.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', () => clearError(input));
        input.addEventListener('change', () => clearError(input));
    });

    form.addEventListener('submit', function (e) {
        let valid = true;

        form.querySelectorAll('[data-required]').forEach(input => {
            if (!input.value.trim()) {
                showError(input, input.dataset.required || 'This field is required.');
                valid = false;
            }
        });

        form.querySelectorAll('[data-minlength]').forEach(input => {
            if (input.value.trim() && input.value.trim().length < parseInt(input.dataset.minlength)) {
                showError(input, `Minimum ${input.dataset.minlength} characters required.`);
                valid = false;
            }
        });

        form.querySelectorAll('[data-numeric]').forEach(input => {
            if (input.value.trim() && isNaN(input.value)) {
                showError(input, 'Must be a valid number.');
                valid = false;
            }
        });

        form.querySelectorAll('[data-min]').forEach(input => {
            if (input.value.trim() && parseFloat(input.value) < parseFloat(input.dataset.min)) {
                showError(input, `Minimum value is ${input.dataset.min}.`);
                valid = false;
            }
        });

        form.querySelectorAll('[data-image]').forEach(input => {
            if (input.files.length > 0) {
                const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowed.includes(input.files[0].type)) {
                    showError(input, 'Only image files are allowed (jpg, png, gif, webp).');
                    valid = false;
                }
                if (input.files[0].size > 2 * 1024 * 1024) {
                    showError(input, 'Image size must be under 2MB.');
                    valid = false;
                }
            }
        });

        form.querySelectorAll('select[data-required]').forEach(select => {
            if (!select.value) {
                showError(select, select.dataset.required || 'Please select an option.');
                valid = false;
            }
        });

        form.querySelectorAll('[data-email]').forEach(input => {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (input.value.trim() && !emailRegex.test(input.value.trim())) {
                showError(input, input.dataset.email || 'Invalid email address.');
                valid = false;
            }
        });

        if (!valid) e.preventDefault();
    });
}

// ✅ Run on initial load (handles both normal and already-loaded DOM)
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initValidation);
} else {
    initValidation();
}

// ✅ Expose globally so AJAX navigation can re-init
window.initValidation = initValidation;