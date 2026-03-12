<x-layouts.layout title="Sugestões de Livros">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold">Sugestões de Livros</h1>
                <p class="text-base-content/70 mt-1">Aprovar ou rejeitar sugestões de livros dos utilizadores</p>
            </div>
            <a href="{{ route('google-books.search.form') }}" class="btn btn-ghost">
                ← Pesquisar Livros
            </a>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        @if(session('erro'))
            <div class="alert alert-error mb-6">{{ session('erro') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-4">
            @forelse($sugestoes as $sugestao)
                <div class="card card-side bg-base-100 shadow-lg">
                    {{-- Capa --}}
                    <figure class="w-24 h-auto flex-shrink-0 bg-base-200">
                        @if($sugestao->capa_thumbnail)
                            <img src="{{ $sugestao->capa_thumbnail }}" alt="{{ $sugestao->titulo }}" class="object-cover w-full h-full">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-base-content/30">
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif
                    </figure>

                    {{-- Conteúdo --}}
                    <div class="card-body">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="card-title text-lg font-bold">{{ $sugestao->titulo }}</h2>
                                <p class="text-sm text-base-content/70">Sugerido por: {{ $sugestao->user->name }}</p>
                            </div>
                            <div>
                                @php
                                    $statusClasses = [
                                        'pendente' => 'badge-warning',
                                        'aprovado' => 'badge-success',
                                        'rejeitado' => 'badge-error',
                                    ];
                                    $statusTexts = [
                                        'pendente' => 'Pendente',
                                        'aprovado' => 'Aprovado',
                                        'rejeitado' => 'Rejeitado',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$sugestao->status] }} badge-lg">
                                    {{ $statusTexts[$sugestao->status] }}
                                </span>
                            </div>
                        </div>

                        <div class="text-sm space-y-1 mt-2">
                            <p><span class="font-bold">Autores:</span> {{ $sugestao->autores }}</p>
                            <p><span class="font-bold">Editora:</span> {{ $sugestao->editora }}</p>
                            <p><span class="font-bold">ISBN:</span> {{ $sugestao->isbn ?? 'N/A' }}</p>
                            <p><span class="font-bold">Data:</span> {{ $sugestao->created_at->format('d/m/Y H:i') }}</p>
                            @if($sugestao->observacoes_admin)
                                <p class="text-error"><span class="font-bold">Observações:</span> {{ $sugestao->observacoes_admin }}</p>
                            @endif
                        </div>

                        @if($sugestao->status === 'pendente')
                            <div class="card-actions justify-end mt-4">
                                {{-- Botão Aprovar (abre modal) --}}
                                <button class="btn btn-success btn-sm" onclick="aprovarModal{{ $sugestao->id }}.showModal()">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aprovar
                                </button>

                                {{-- Botão Rejeitar (abre modal) --}}
                                <button class="btn btn-error btn-sm" onclick="rejeitarModal{{ $sugestao->id }}.showModal()">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Rejeitar
                                </button>

                                {{-- MODAL APROVAR --}}
                                <dialog id="aprovarModal{{ $sugestao->id }}" class="modal modal-bottom sm:modal-middle">
                                    <div class="modal-box">
                                        <h3 class="text-lg font-bold text-success">Aprovar Sugestão</h3>

                                        <form action="{{ route('google-books.sugestoes.aprovar', $sugestao) }}" method="POST" id="aprovarForm{{ $sugestao->id }}">
                                            @csrf
                                            <div class="py-4">
                                                <p>Confirmas que desejas aprovar a sugestão do livro</p>
                                                <p class="font-bold text-lg my-2">"{{ $sugestao->titulo }}"</p>
                                                <p class="text-sm text-base-content/70">Este livro será adicionado à biblioteca.</p>
                                            </div>
                                        </form>

                                        <div class="modal-action">
                                            <button class="btn btn-ghost" onclick="document.getElementById('aprovarModal{{ $sugestao->id }}').close()">
                                                Cancelar
                                            </button>
                                            <button type="submit" form="aprovarForm{{ $sugestao->id }}" class="btn btn-success">
                                                Confirmar Aprovação
                                            </button>
                                        </div>
                                    </div>
                                </dialog>

                                {{-- MODAL REJEITAR --}}
                                <dialog id="rejeitarModal{{ $sugestao->id }}" class="modal modal-bottom sm:modal-middle">
                                    <div class="modal-box">
                                        <h3 class="text-lg font-bold text-error">Rejeitar Sugestão</h3>

                                        <form action="{{ route('google-books.sugestoes.rejeitar', $sugestao) }}" method="POST" id="rejeitarForm{{ $sugestao->id }}">
                                            @csrf
                                            <div class="py-4">
                                                <p class="mb-4">Tens a certeza que desejas rejeitar a sugestão do livro</p>
                                                <p class="font-bold text-lg my-2">"{{ $sugestao->titulo }}"</p>

                                                <label class="form-control mt-4">
                                                    <span class="label-text">Motivo da rejeição</span>
                                                    <textarea name="motivo" class="textarea textarea-bordered h-24 mt-2"
                                                              placeholder="Ex: Livro já existe, dados incompletos..." required></textarea>
                                                </label>
                                            </div>
                                        </form>

                                        <div class="modal-action">
                                            <button class="btn btn-ghost" onclick="document.getElementById('rejeitarModal{{ $sugestao->id }}').close()">
                                                Cancelar
                                            </button>
                                            <button type="submit" form="rejeitarForm{{ $sugestao->id }}" class="btn btn-error">
                                                Confirmar Rejeição
                                            </button>
                                        </div>
                                    </div>
                                </dialog>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-base-200 rounded-box">
                    <svg class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="text-base-content/60 text-xl">Nenhuma sugestão encontrada</p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if($sugestoes->hasPages())
            <div class="mt-6 flex justify-center">
                <div class="join">
                    @if($sugestoes->onFirstPage())
                        <button class="join-item btn btn-disabled">«</button>
                    @else
                        <a href="{{ $sugestoes->previousPageUrl() }}" class="join-item btn">«</a>
                    @endif

                    @foreach($sugestoes->getUrlRange(1, $sugestoes->lastPage()) as $page => $url)
                        @if($page >= $sugestoes->currentPage() - 2 && $page <= $sugestoes->currentPage() + 2)
                            <a href="{{ $url }}" class="join-item btn {{ $page == $sugestoes->currentPage() ? 'btn-active' : '' }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if($sugestoes->hasMorePages())
                        <a href="{{ $sugestoes->nextPageUrl() }}" class="join-item btn">»</a>
                    @else
                        <button class="join-item btn btn-disabled">»</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.layout>
