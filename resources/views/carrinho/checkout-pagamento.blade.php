<x-layouts.layout title="Checkout - Pagamento">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('checkout.morada') }}" class="btn btn-ghost btn-circle">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold">Checkout - Passo 2 de 2</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Formulário de pagamento Stripe --}}
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Pagamento com Cartão</h2>

                        <div class="alert alert-info mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Ambiente de testes - Usa o cartão 4242 4242 4242 4242, data futura, CVC 123</span>
                        </div>

                        <form id="payment-form" action="{{ route('checkout.confirmar') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_intent_id" id="payment-intent-id" value="{{ $paymentIntent->id }}">
                            <input type="hidden" name="payment_method_id" id="payment-method-id">

                            <div id="payment-element" class="mb-6"></div>

                            <div id="card-errors" class="text-error mb-4" role="alert"></div>

                            <button id="submit-button" class="btn btn-info w-full">
                                <span id="button-text">Pagar € {{ number_format($totalComIva, 2, ',', '.') }}</span>
                                <span id="spinner" class="loading loading-spinner loading-sm hidden"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Resumo --}}
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-lg sticky top-6">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Detalhes da Encomenda</h2>

                        <div class="space-y-4">
                            @foreach($itens as $item)
                                <div class="flex gap-2">
                                    <div class="w-12 h-16 flex-shrink-0">
                                        <img src="{{ $item->livro->imagem_capa_url }}"
                                             alt="{{ $item->livro->nome }}"
                                             class="w-full h-full object-cover rounded">
                                    </div>
                                    <div class="flex-1 text-sm">
                                        <p class="font-medium line-clamp-2">{{ $item->livro->nome }}</p>
                                        <p class="text-xs text-base-content/50">{{ $item->quantidade }} x € {{ number_format($item->livro->preco, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="divider my-2"></div>

                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span>€ {{ number_format($total, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>IVA (23%):</span>
                                    <span>€ {{ number_format($iva, 2, ',', '.') }}</span>
                                </div>
                                <div class="divider my-2"></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-success">€ {{ number_format($totalComIva, 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mt-4 p-4 bg-base-200 rounded-box">
                                <h3 class="font-bold mb-2">Morada de Entrega</h3>
                                <p class="text-sm">{{ $morada['morada'] }}</p>
                                <p class="text-sm">{{ $morada['cidade'] }}, {{ $morada['codigo_postal'] }}</p>
                                <p class="text-sm">{{ $morada['telefone'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM carregado, a iniciar Stripe...');

            const stripe = Stripe('{{ env('STRIPE_KEY') }}');
            const elements = stripe.elements({
                clientSecret: '{{ $paymentIntent->client_secret }}'
            });

            const paymentElement = elements.create('payment');

            // Verifica se o elemento existe antes de montar
            const elementContainer = document.getElementById('payment-element');
            if (!elementContainer) {
                console.error('Elemento #payment-element não encontrado!');
                return;
            }

            paymentElement.mount('#payment-element');

            const form = document.getElementById('payment-form');
            const submitButton = document.getElementById('submit-button');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('button-text');
            const cardErrors = document.getElementById('card-errors');

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                console.log('FORM SUBMETIDO!');

                submitButton.disabled = true;
                spinner.classList.remove('hidden');
                buttonText.classList.add('hidden');

                const { error, paymentIntent } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: '{{ route('encomendas.sucesso') }}',
                    },
                    redirect: 'if_required',
                });

                console.log('Resposta do Stripe:', { error, paymentIntent });

                if (error) {
                    console.error('Erro:', error);
                    cardErrors.textContent = error.message;
                    submitButton.disabled = false;
                    spinner.classList.add('hidden');
                    buttonText.classList.remove('hidden');
                } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                    console.log('Pagamento bem sucedido!');
                    document.getElementById('payment-intent-id').value = paymentIntent.id;
                    document.getElementById('payment-method-id').value = paymentIntent.payment_method;
                    form.submit();
                } else {
                    console.log('Aguardando redirecionamento...');
                }
            });
        });
    </script>
    @endpushs
</x-layouts.layout>
