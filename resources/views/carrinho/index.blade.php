<x-layouts.layout title="Meu Carrinho">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Meu Carrinho</h1>
            <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                ← Continuar a Comprar
            </a>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        @if(session('erro'))
            <div class="alert alert-error mb-6">{{ session('erro') }}</div>
        @endif

        @if($itens->isEmpty())
            <div class="text-center py-16 bg-base-200 rounded-box">
                <svg class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-base-content/60 text-xl mb-4">O teu carrinho está vazio</p>
                <a href="{{ route('livros.index') }}" class="btn btn-info">Explorar Livros</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Lista de itens --}}
                <div class="lg:col-span-2 space-y-4">
                    @foreach($itens as $item)
                        <div class="card card-side bg-base-100 shadow-lg">
                            <figure class="w-24 h-auto flex-shrink-0">
                                <img src="{{ $item->livro->imagem_capa_url }}"
                                     alt="{{ $item->livro->nome }}"
                                     class="w-full h-full object-cover">
                            </figure>
                            <div class="card-body">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-bold">{{ $item->livro->nome }}</h3>
                                        <p class="text-sm text-base-content/70">
                                            {{ $item->livro->autores->first()->nome ?? 'Autor desconhecido' }}
                                        </p>
                                    </div>
                                    <p class="text-lg font-bold text-success">
                                        € {{ number_format($item->livro->preco, 2, ',', '.') }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('carrinho.atualizar', $item) }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            @method('PUT')
                                            <select name="quantidade" class="select select-sm select-bordered w-20" onchange="this.form.submit()">
                                                @for($i = 1; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ $item->quantidade == $i ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </form>
                                        <span class="text-sm text-base-content/50">x € {{ number_format($item->livro->preco, 2, ',', '.') }}</span>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <span class="font-bold">
                                            € {{ number_format($item->livro->preco * $item->quantidade, 2, ',', '.') }}
                                        </span>
                                        <form action="{{ route('carrinho.remover', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-ghost text-error">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Resumo do carrinho --}}
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-lg sticky top-6">
                        <div class="card-body">
                            <h2 class="card-title text-xl mb-4">Resumo da Encomenda</h2>

                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>Subtotal ({{ $itens->sum('quantidade') }} itens):</span>
                                    <span class="font-bold">€ {{ number_format($total, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>IVA (23%):</span>
                                    <span>€ {{ number_format($iva, 2, ',', '.') }}</span>
                                </div>
                                <div class="divider my-2"></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-success">€ {{ number_format($totalComIva, 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="mt-6 space-y-2">
                                <a href="{{ route('checkout.morada') }}" class="btn btn-info w-full">
                                    Finalizar Encomenda
                                </a>
                                <a href="{{ route('livros.index') }}" class="btn btn-ghost w-full">
                                    Continuar a Comprar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.layout>
