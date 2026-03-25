<x-layouts.layout title="Encomenda Realizada com Sucesso">
    <div class="max-w-2xl mx-auto text-center">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="mb-6 text-success">
                    <svg class="h-24 w-24 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold mb-4">Encomenda Realizada com Sucesso!</h1>

                <p class="text-base-content/70 mb-6">
                    Obrigado pela tua compra. Vais receber um email com os detalhes da encomenda.
                </p>

                @if(isset($numero_encomenda))
                    <div class="stats shadow mb-6">
                        <div class="stat">
                            <div class="stat-title">Nº Encomenda</div>
                            <div class="stat-value text-2xl">{{ $numero_encomenda }}</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Total</div>
                            <div class="stat-value text-2xl text-success">€ {{ number_format($total ?? 0, 2, ',', '.') }}</div>
                        </div>
                    </div>
                @endif

                <div class="flex gap-4 justify-center">
                    <a href="{{ route('livros.index') }}" class="btn btn-info">
                        Continuar a Comprar
                    </a>
                    <a href="{{ route('encomendas.minhas') }}" class="btn btn-ghost">
                        Ver Minhas Encomendas
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
