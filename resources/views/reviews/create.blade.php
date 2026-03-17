<x-layouts.layout title="Avaliar Livro">
    <div class="max-w-2xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Avaliar Livro</h2>

                <div class="flex gap-6 mb-6 p-4 bg-base-200 rounded-box">
                    <div class="w-20 h-28">
                        <img src="{{ $requisicao->livro->imagem_capa_url }}"
                             alt="{{ $requisicao->livro->nome }}"
                             class="w-full h-full object-cover rounded">
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">{{ $requisicao->livro->nome }}</h3>
                        <p class="text-sm text-base-content/70">
                            Requisição: {{ $requisicao->codigo }}<br>
                            Data de entrega: {{ $requisicao->data_devolucao_real->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <form action="{{ route('reviews.store', $requisicao) }}" method="POST">
                    @csrf

                    {{-- Rating (estrelas completas) --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Classificação</span>
                        </label>

                        <div class="flex flex-col items-center gap-2">
                            {{-- Estrelas visuais --}}
                            <div class="rating rating-lg" id="estrelas-visual">
                                <input type="radio" name="rating" value="1" class="mask mask-star-2 bg-orange-400" aria-label="1 estrela" />
                                <input type="radio" name="rating" value="2" class="mask mask-star-2 bg-orange-400" aria-label="2 estrelas" />
                                <input type="radio" name="rating" value="3" class="mask mask-star-2 bg-orange-400" aria-label="3 estrelas" />
                                <input type="radio" name="rating" value="4" class="mask mask-star-2 bg-orange-400" aria-label="4 estrelas" />
                                <input type="radio" name="rating" value="5" class="mask mask-star-2 bg-orange-400" aria-label="5 estrelas" />
                            </div>

                            {{-- Texto explicativo --}}
                            <p class="text-sm text-base-content/70">
                                Clique nas estrelas para classificar (1 a 5)
                            </p>
                        </div>

                        @error('rating')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Comentário --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Comentário (opcional)</span>
                        </label>
                        <textarea name="comentario" rows="4" class="textarea textarea-bordered"
                                  placeholder="Partilha a tua opinião sobre o livro...">{{ old('comentario') }}</textarea>
                        @error('comentario')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>A tua review ficará suspensa até aprovação de um administrador.</span>
                    </div>

                    <div class="card-actions justify-end gap-4">
                        <a href="{{ route('requisicoes.show', $requisicao) }}" class="btn btn-ghost">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-info">
                            Submeter Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
