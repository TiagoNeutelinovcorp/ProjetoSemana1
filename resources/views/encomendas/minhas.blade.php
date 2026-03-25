<x-layouts.layout title="Minhas Encomendas">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Minhas Encomendas</h1>
            <a href="{{ route('profile.index') }}" class="btn btn-ghost">
                Voltar ao Perfil
            </a>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        @if(session('erro'))
            <div class="alert alert-error mb-6">{{ session('erro') }}</div>
        @endif

        @if($encomendas->isEmpty())
            <div class="text-center py-16 bg-base-200 rounded-box">
                <svg class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-base-content/60 text-xl mb-4">Ainda não tens encomendas</p>
                <a href="{{ route('livros.index') }}" class="btn btn-info">Explorar Livros</a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($encomendas as $encomenda)
                    <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all">
                        <div class="card-body">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h2 class="text-lg font-bold">Encomenda #{{ $encomenda->numero_encomenda }}</h2>
                                        @php
                                            $statusCores = [
                                                'pendente_pagamento' => 'badge-warning',
                                                'pago' => 'badge-info',
                                                'processando' => 'badge-info',
                                                'enviado' => 'badge-primary',
                                                'entregue' => 'badge-success',
                                                'cancelado' => 'badge-error',
                                            ];
                                            $statusTextos = [
                                                'pendente_pagamento' => 'Aguardar Pagamento',
                                                'pago' => 'Pago',
                                                'processando' => 'Processando',
                                                'enviado' => 'Enviado',
                                                'entregue' => 'Entregue',
                                                'cancelado' => 'Cancelado',
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusCores[$encomenda->status] ?? 'badge-ghost' }} badge-sm">
                                            {{ $statusTextos[$encomenda->status] ?? $encomenda->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-base-content/70 mt-1">
                                        {{ $encomenda->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-success">
                                        € {{ number_format($encomenda->total, 2, ',', '.') }}
                                    </p>
                                    <p class="text-sm text-base-content/70">
                                        {{ $encomenda->itens->sum('quantidade') }} livro(s)
                                    </p>
                                </div>
                            </div>

                            {{-- Miniaturas dos livros --}}
                            <div class="flex gap-2 mt-4 overflow-x-auto py-2">
                                @foreach($encomenda->itens->take(5) as $item)
                                    <div class="w-12 h-16 flex-shrink-0">
                                        <img src="{{ $item->livro->imagem_capa_url }}"
                                             alt="{{ $item->livro->nome }}"
                                             class="w-full h-full object-cover rounded">
                                    </div>
                                @endforeach
                                @if($encomenda->itens->count() > 5)
                                    <div class="w-12 h-16 bg-base-200 rounded flex items-center justify-center">
                                        <span class="text-xs font-bold">+{{ $encomenda->itens->count() - 5 }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="card-actions justify-end mt-4">
                                <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-info btn-sm">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginação --}}
            @if($encomendas->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $encomendas->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.layout>
