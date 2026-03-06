<x-layouts.layout title="Nova Requisição">
    <div class="max-w-2xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Confirmar Requisição</h2>

                <div class="flex gap-6 mb-6">
                    <div class="w-32 h-40">
                        <img src="{{ $livro->imagem_capa_url }}" alt="{{ $livro->nome }}"
                             class="w-full h-full object-cover rounded">
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold">{{ $livro->nome }}</h3>
                        <p class="text-base-content/70">ISBN: {{ $livro->isbn }}</p>
                        <p class="text-base-content/70">Editora: {{ $livro->editora->nome }}</p>
                        <p class="text-lg font-bold text-info mt-2">{{ $livro->preco_formatado }}</p>
                    </div>
                </div>

                <div class="alert alert-info mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Ao confirmar, este livro ficará reservado por 5 dias. A data de devolução prevista é {{ now()->addDays(5)->format('d/m/Y') }}.</span>
                </div>

                <form action="{{ route('requisicoes.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="livro_id" value="{{ $livro->id }}">

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('livros.show', $livro) }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            Confirmar Requisição
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
