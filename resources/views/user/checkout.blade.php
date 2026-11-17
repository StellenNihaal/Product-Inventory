<x-user-layout>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-xl font-semibold text-gray-800 mb-6">Checkout</h1>

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                {{ session('error') }}
            </div>
        @endif
        {{-- Order Summary --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5 mb-6">
            <h2 class="font-medium text-gray-800 mb-4">Order Summary</h2>
            @foreach ($cartItems as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3">
                        @if ($item->product->image)
                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                class="w-10 h-10 object-contain rounded-lg border border-gray-100">
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $item->product->name }}</p>
                            <p class="text-xs text-gray-400">Qty: {{ $item->quantity }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">
                        ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                    </p>
                </div>
            @endforeach
            <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                <p class="font-semibold text-gray-800">Total</p>
                <p class="text-xl font-bold text-green-600">₹{{ number_format($total, 2) }}</p>
            </div>
        </div>

        {{-- Payment Method Tabs --}}
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <h2 class="font-medium text-gray-800 mb-4">Choose Payment Method</h2>

            {{-- Tab Buttons --}}
            <div class="flex gap-3 mb-5">
                <button onclick="showTab('card')" id="tab-card"
                    class="flex-1 py-2.5 rounded-lg text-sm font-medium border-2 border-green-500 bg-green-50 text-green-600 transition">
                    💳 Pay by Card
                </button>
            </div>

            {{-- Card Payment Tab --}}
            <div id="tab-card-content">

                {{-- Cardholder Name --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                    <input type="text" id="cardholder-name" value="{{ auth()->user()->name }}" placeholder="John Doe"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300">
                </div>

                {{-- Card Number --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                    <div id="card-number-element"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2.5 min-h-[40px]"></div>
                </div>

                {{-- Expiry & CVC --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <div id="card-expiry-element"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 min-h-[40px]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                        <div id="card-cvc-element"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 min-h-[40px]"></div>
                    </div>
                </div>

                {{-- Card Error --}}
                <div id="card-errors" class="text-red-400 text-xs mb-3 hidden"></div>

                {{-- Accepted Cards --}}
                <div class="flex gap-2 mb-4">
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">💳 Visa</span>
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">💳 Mastercard</span>
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">💳 Amex</span>
                </div>

                {{-- Pay Button --}}
                <button id="pay-button"
                    class="w-full bg-green-500 text-white py-3 rounded-lg font-semibold hover:bg-green-600 transition text-sm">
                    <span id="pay-button-text">Pay ₹{{ number_format($total, 2) }}</span>
                    <span id="pay-spinner" class="hidden">Processing...</span>
                </button>
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-3">
            🔒 Card payments secured by Stripe. Your info is never stored.
        </p>
    </div>

    {{-- Stripe JS --}}
    {{-- No paymentIntent passed from PHP anymore --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        window.addEventListener('load', function() {

            const stripe = Stripe('{{ config('services.stripe.key') }}');
            const elements = stripe.elements();

            const style = {
                base: {
                    fontSize: '14px',
                    color: '#374151',
                    fontFamily: 'ui-sans-serif, system-ui, sans-serif',
                    '::placeholder': {
                        color: '#9ca3af'
                    },
                },
                invalid: {
                    color: '#ef4444'
                },
            };

            const cardNumber = elements.create('cardNumber', {
                style
            });
            cardNumber.mount('#card-number-element');

            const cardExpiry = elements.create('cardExpiry', {
                style
            });
            cardExpiry.mount('#card-expiry-element');

            const cardCvc = elements.create('cardCvc', {
                style
            });
            cardCvc.mount('#card-cvc-element');

            [cardNumber, cardExpiry, cardCvc].forEach(el => {
                el.addEventListener('change', function(event) {
                    const errorDiv = document.getElementById('card-errors');
                    if (event.error) {
                        errorDiv.textContent = event.error.message;
                        errorDiv.classList.remove('hidden');
                    } else {
                        errorDiv.classList.add('hidden');
                    }
                });
            });

            document.getElementById('pay-button').addEventListener('click', async function() {
                const button = document.getElementById('pay-button');
                const buttonText = document.getElementById('pay-button-text');
                const spinner = document.getElementById('pay-spinner');
                const errorDiv = document.getElementById('card-errors');

                button.disabled = true;
                buttonText.classList.add('hidden');
                spinner.classList.remove('hidden');

                try {
                    // ✅ Step 1: Create PaymentIntent via AJAX
                    const res = await fetch('{{ route('user.checkout.intent') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({}),
                    });

                    const data = await res.json();

                    if (data.error) {
                        errorDiv.textContent = data.error;
                        errorDiv.classList.remove('hidden');
                        button.disabled = false;
                        buttonText.classList.remove('hidden');
                        spinner.classList.add('hidden');
                        return;
                    }

                    // ✅ Step 2: Confirm payment with card details
                    const {
                        error,
                        paymentIntent
                    } = await stripe.confirmCardPayment(data.client_secret, {
                        payment_method: {
                            card: cardNumber,
                            billing_details: {
                                name: document.getElementById('cardholder-name').value,
                                email: '{{ auth()->user()->email }}',
                            },
                        },
                    });

                    if (error) {
                        errorDiv.textContent = error.message;
                        errorDiv.classList.remove('hidden');
                        button.disabled = false;
                        buttonText.classList.remove('hidden');
                        spinner.classList.add('hidden');
                    } else if (paymentIntent.status === 'succeeded') {
                        // ✅ Step 3: Submit payment_intent_id to success route
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('user.checkout.success') }}';

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);

                        const piInput = document.createElement('input');
                        piInput.type = 'hidden';
                        piInput.name = 'payment_intent_id';
                        piInput.value = data.payment_intent_id;
                        form.appendChild(piInput);

                        document.body.appendChild(form);
                        form.submit();
                    }

                } catch (err) {
                    errorDiv.textContent = 'Something went wrong. Please try again.';
                    errorDiv.classList.remove('hidden');
                    button.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                }
            });
        });
    </script>
</x-user-layout>
