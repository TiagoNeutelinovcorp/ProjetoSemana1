<x-layouts.layout title="Detalhe da Review">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Detalhe da Review #{{ $review->id }}</h1>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost">
                ← Voltar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Coluna principal --}}
            <div class="md:col-span-2 space-y-6">
                {{-- Informações da Review --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Review</h2>

                        <div class="space-y-4">
                            <div>
                                <span class="font-bold">Classificação:</span>
                                <div class="rating rating-md mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <span class="mask mask-star-2 bg-orange-400"></span>
                                        @else
                                            <span class="mask mask-star-2 bg-gray-300"></span>
                                        @endif
                                    @endfor
                                </div>
                            </div>

                            @if($review->comentario)
                                <div>
                                    <span class="font-bold">Comentário:</span>
                                    <p class="mt-1 p-3 bg-base-200 rounded-lg">{{ $review->comentario }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="font-bold">Status:</span>
                                    @php
                                        $statusClasses = [
                                            'suspenso' => 'badge-warning',
                                            'ativo' => 'badge-success',
                                            'recusado' => 'badge-error',
                                        ];
                                        $statusTexts = [
                                            'suspenso' => 'Pendente',
                                            'ativo' => 'Ativo',
                                            'recusado' => 'Recusado',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusClasses[$review->status] }} badge-md ml-2">
                                        {{ $statusTexts[$review->status] }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-bold">Data:</span>
                                    <span class="ml-2">{{ $review->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>

                            @if($review->status === 'recusado' && $review->justificacao_recusa)
                                <div class="alert alert-error">
                                    <span class="font-bold">Justificação da recusa:</span>
                                    <p class="mt-1">{{ $review->justificacao_recusa }}</p>
                                </div>
                            @endif

                            @if($review->status === 'ativo' && $review->aprovador)
                                <div class="alert alert-success text-sm">
                                    Aprovado por {{ $review->aprovador->name }} em {{ $review->aprovado_em->format('d/m/Y H:i') }}
                                </div>
                            @endif
                        </div>

                        {{-- Ações para reviews pendentes --}}
                        @if($review->status === 'suspenso')
                            <div class="card-actions justify-end gap-2 mt-6">
                                {{-- Botão Aprovar --}}
                                <form action="{{ route('admin.reviews.aprovar', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success"
                                            onclick="return confirm('Confirmar aprovação desta review?')">
                                        Aprovar Review
                                    </button>
                                </form>

                                {{-- Botão Recusar (abre modal) --}}
                                <button class="btn btn-error" onclick="recusarModal.showModal()">
                                    Recusar Review
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Informações do Cidadão --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Cidadão</h2>

                        <div class="flex items-center gap-4">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-full w-12 h-12">
                                    <span>{{ substr($review->user->name, 0, 1) }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-bold">{{ $review->user->name }}</p>
                                <p class="text-sm text-base-content/70">{{ $review->user->email }}</p>
                            </div>
                        </div>

                        <a href="{{ route('requisicoes.historico', $review->user) }}"
                           class="btn btn-outline btn-sm w-full mt-4">
                            Ver Histórico
                        </a>
                    </div>
                </div>

                {{-- Informações do Livro --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Livro</h2>

                        <div class="flex gap-4">
                            <div class="w-16 h-20">
                                <img src="{{ $review->livro->imagem_capa_url }}"
                                     alt="{{ $review->livro->nome }}"
                                     class="w-full h-full object-cover rounded">
                            </div>
                            <div>
                                <p class="font-bold">{{ $review->livro->nome }}</p>
                                <p class="text-sm text-base-content/70">ISBN: {{ $review->livro->isbn }}</p>
                                <p class="text-sm text-base-content/70">Editora: {{ $review->livro->editora->nome }}</p>
                            </div>
                        </div>

                        <a href="{{ route('livros.show', $review->livro) }}" class="btn btn-outline btn-sm w-full mt-4">
                            Ver Livro
                        </a>
                    </div>
                </div>

                {{-- Informações da Requisição --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Requisição</h2>

                        <p><span class="font-bold">Código:</span> {{ $review->requisicao->codigo }}</p>
                        <p><span class="font-bold">Data req.:</span> {{ $review->requisicao->data_requisicao->format('d/m/Y') }}</p>
                        <p><span class="font-bold">Data dev.:</span> {{ $review->requisicao->data_devolucao_real?->format('d/m/Y') }}</p>

                        <a href="{{ route('requisicoes.show', $review->requisicao) }}" class="btn btn-outline btn-sm w-full mt-4">
                            Ver Requisição
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de recusa --}}
    <dialog id="recusarModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-error">Recusar Review</h3>

            <form action="{{ route('admin.reviews.recusar', $review) }}" method="POST" id="recusarForm">
                @csrf
                <div class="py-4">
                    <p class="mb-4">Tens a certeza que desejas recusar esta review?</p>

                    <label class="form-control">
                        <span class="label-text">Justificação da recusa</span>
                        <textarea name="justificacao" class="textarea textarea-bordered h-24 mt-2"
                                  placeholder="Ex: Comentário inadequado, fora do contexto..." required></textarea>
                    </label>
                </div>
            </form>

            <div class="modal-action">
                <button class="btn btn-ghost" onclick="document.getElementById('recusarModal').close()">
                    Cancelar
                </button>
                <button type="submit" form="recusarForm" class="btn btn-error">
                    Confirmar Recusa
                </button>
            </div>
        </div>
    </dialog>
</x-layouts.layout>
