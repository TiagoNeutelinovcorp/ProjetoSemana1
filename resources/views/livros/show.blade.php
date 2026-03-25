<x-layouts.layout title="{{ $livro->nome }}">
    <div class="max-w-5xl mx-auto">
        {{-- Botão voltar --}}
        <div class="mb-4">
            <a href="{{ route('livros.index') }}" class="btn btn-ghost btn-sm">
                ← Voltar para a lista
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                {{-- Cabeçalho com título e ações --}}
                <div class="flex justify-between items-start mb-6">
                    <h1 class="text-3xl font-bold">{{ $livro->nome }}</h1>

                    @auth
                        @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                            <div class="flex gap-2">
                                <a href="{{ route('livros.edit', $livro) }}" class="btn btn-warning btn-sm">
                                    Editar
                                </a>
                                <form action="{{ route('livros.destroy', $livro) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error btn-sm"
                                            onclick="return confirm('Tem a certeza que deseja apagar este livro?')">
                                        Apagar
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                {{-- Grid: Imagem + Informações --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Coluna da Imagem com Efeito 3D --}}
                    <div class="md:col-span-1">
                        <div class="sticky top-6">
                            <div class="hover-3d w-full">
                                <figure class="w-full rounded-2xl">
                                    <img
                                        src="{{ $livro->imagem_capa_url }}"
                                        alt="{{ $livro->nome }}"
                                        class="w-full h-auto object-cover rounded-2xl shadow-2xl"
                                    />
                                </figure>
                                <div></div><div></div><div></div><div></div>
                                <div></div><div></div><div></div><div></div>
                            </div>

                            <div class="mt-4 text-center md:hidden">
                                <span class="text-3xl font-bold text-white">{{ $livro->preco_formatado }}</span>
                            </div>

                            <div class="mt-4 text-center">
                                @if($livro->isDisponivel())
                                    <span class="badge badge-success badge-lg">Disponível para requisição</span>
                                @else
                                    <span class="badge badge-error badge-lg">Indisponível</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Coluna das Informações --}}
                    <div class="md:col-span-2 space-y-6">
                        <div class="hidden md:block">
                            <span class="text-4xl font-bold text-white">{{ $livro->preco_formatado }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="font-bold w-32">ISBN</td>
                                    <td>{{ $livro->isbn }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold">Editora</td>
                                    <td>
                                        @if($livro->editora)
                                            <a href="{{ route('editoras.show', $livro->editora) }}" class="link link-white">
                                                {{ $livro->editora->nome }}
                                            </a>
                                        @else
                                            <span class="text-base-content/50">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-bold">Autores</td>
                                    <td>
                                        @if($livro->autores->count() > 0)
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($livro->autores as $autor)
                                                    <a href="{{ route('autores.show', $autor) }}" class="badge badge-info badge-outline p-3">
                                                        {{ $autor->nome }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-base-content/50">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex flex-wrap gap-3 mt-4">
                            @auth
                                @if(auth()->user()->two_factor_secret)
                                    @if($livro->isDisponivel())
                                        <a href="{{ route('requisicoes.create', $livro) }}" class="btn btn-success">
                                            Requisitar Livro
                                        </a>
                                    @endif
                                @else
                                    <button class="btn btn-success" onclick="twofaWarningModal.showModal()">
                                        Requisitar Livro
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-success" onclick="loginRequisitarModal.showModal()">
                                    Requisitar Livro
                                </button>
                            @endauth

                            @auth
                                @if(auth()->user()->isBibliotecario())
                                    @if(auth()->user()->two_factor_secret)
                                        <a href="{{ route('livros.historico', $livro) }}" class="btn btn-info">
                                            Ver Histórico
                                        </a>
                                    @else
                                        <button class="btn btn-info" onclick="twofaHistoricoModal.showModal()">
                                            Ver Histórico
                                        </button>
                                    @endif
                                @endif
                            @endauth

                                {{-- Botão de compra --}}
                            @auth
                                 @if(auth()->user()->two_factor_secret)
                                    @if($livro->isDisponivel())
                                        <form action="{{ route('carrinho.adicionar', $livro) }}" method="POST" class="inline">
                                            @csrf
                                                <button type="submit" class="btn btn-primary">
                                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    Comprar
                                                </button>
                                        </form>
                                    @endif
                                 @endif
                            @endauth

                            {{-- BOTÃO DE ALERTA PARA LIVROS INDISPONÍVEIS --}}
                            @auth
                                @if(auth()->user()->two_factor_secret && !$livro->isDisponivel())
                                    @php
                                        $alertaAtivo = auth()->user()->alertasPendentes()
                                            ->where('livro_id', $livro->id)
                                            ->exists();
                                    @endphp

                                    @if(!$alertaAtivo)
                                        <form action="{{ route('alertas.store', $livro) }}" method="POST" class="mt-2 w-full">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm w-full">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                                Avise-me quando disponível
                                            </button>
                                        </form>
                                    @else
                                        <div class="mt-2 flex items-center gap-2 w-full">
                                            <span class="badge badge-warning badge-sm">Alerta ativo</span>
                                            @php
                                                $alerta = auth()->user()->alertasPendentes()
                                                    ->where('livro_id', $livro->id)
                                                    ->first();
                                            @endphp
                                            @if($alerta)
                                                <form action="{{ route('alertas.cancelar', $alerta) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-ghost text-error">
                                                        Cancelar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            @endauth
                        </div>

                        @if($livro->bibliografia)
                            <div class="divider"></div>
                            <div>
                                <h3 class="text-lg font-bold mb-3">Bibliografia / Descrição</h3>
                                <div class="bg-base-200 p-4 rounded-lg">
                                    <p class="text-base-content/80 leading-relaxed whitespace-pre-line">
                                        {{ $livro->bibliografia }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- SECÇÃO DE LIVROS RELACIONADOS --}}
        @if(isset($livrosRelacionados) && $livrosRelacionados->isNotEmpty())
            <div class="mt-12">
                <h2 class="text-2xl font-bold mb-6">Livros Relacionados</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($livrosRelacionados as $relacionado)
                        <div class="card card-side bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 h-auto min-h-[140px]">
                            {{-- Imagem --}}
                            <figure class="w-20 h-auto min-h-[140px] flex-shrink-0 bg-base-200">
                                <img src="{{ $relacionado->imagem_capa_url }}"
                                     alt="{{ $relacionado->nome }}"
                                     class="w-full h-full object-cover">
                            </figure>

                            {{-- Conteúdo --}}
                            <div class="card-body p-3">
                                <h3 class="card-title text-sm font-bold line-clamp-2">
                                    {{ $relacionado->nome }}
                                </h3>

                                <div class="text-xs space-y-0.5">
                                    <p class="truncate">
                                        <span class="font-bold">Au:</span>
                                        @if($relacionado->autores->isNotEmpty())
                                            {{ $relacionado->autores->first()->nome }}
                                            @if($relacionado->autores->count() > 1)
                                                <span class="badge badge-xs">+{{ $relacionado->autores->count() - 1 }}</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                    <p class="text-success text-xs font-bold">
                                        {{ $relacionado->preco_formatado }}
                                    </p>
                                </div>

                                <div class="card-actions justify-end mt-2">
                                    <a href="{{ route('livros.show', $relacionado) }}" class="btn btn-xs btn-info">
                                        Ver
                                    </a>
                                    @if($relacionado->isDisponivel())
                                        <a href="{{ route('requisicoes.create', $relacionado) }}" class="btn btn-xs btn-success">
                                            Requisitar
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SECÇÃO DE REVIEWS --}}
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Reviews do Livro</h2>

            @if($reviews->count() > 0)
                {{-- Cabeçalho com média --}}
                <div class="flex items-center gap-4 mb-6 p-4 bg-base-200 rounded-box">
                    <div class="text-center">
                        <span class="text-4xl font-bold">{{ number_format($livro->rating_medio, 1) }}</span>
                        <span class="text-base-content/70">/5</span>
                    </div>
                    <div>
                        <div class="rating rating-md rating-half">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($livro->rating_medio))
                                    <span class="mask mask-star-2 bg-orange-400"></span>
                                @elseif($i - $livro->rating_medio <= 0.5)
                                    <span class="mask mask-star-2 mask-half-1 bg-orange-400"></span>
                                @else
                                    <span class="mask mask-star-2 bg-gray-300"></span>
                                @endif
                            @endfor
                        </div>
                        <p class="text-sm text-base-content/70">{{ $reviews->total() }} avaliações</p>
                    </div>
                </div>

                {{-- LISTA DE REVIEWS --}}
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="flex gap-4 p-4 bg-base-100 rounded-box shadow-sm hover:shadow-md transition-shadow">
                            {{-- Foto do utilizador --}}
                            <div class="flex-shrink-0">
                                @php
                                    $user = $review->user;
                                    $userName = $user ? $user->name : 'Utilizador desconhecido';
                                    $userInitial = $user ? substr($user->name, 0, 1) : '?';
                                    $userPhoto = ($user && $user->profile_photo_url) ? $user->profile_photo_url : null;
                                @endphp

                                <div class="avatar">
                                    <div class="w-12 h-12 rounded-full ring ring-primary ring-offset-base-100 ring-offset-1">
                                        @if($userPhoto)
                                            <img src="{{ $userPhoto }}" alt="{{ $userName }}" />
                                        @else
                                            <div class="bg-neutral text-neutral-content text-lg flex items-center justify-center w-full h-full">
                                                {{ $userInitial }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Conteúdo da review --}}
                            <div class="flex-1 min-w-0">
                                {{-- Nome do utilizador (opcional) --}}
                                @if($user)
                                    <p class="text-sm font-medium">{{ $user->name }}</p>
                                @endif

                                <div class="rating rating-xs mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <span class="mask mask-star-2 bg-orange-400"></span>
                                        @else
                                            <span class="mask mask-star-2 bg-gray-300"></span>
                                        @endif
                                    @endfor
                                </div>

                                @if($review->comentario)
                                    <p class="text-sm text-base-content/80 leading-relaxed">
                                        "{{ $review->comentario }}"
                                    </p>
                                @else
                                    <p class="text-sm text-base-content/50 italic">Sem comentário</p>
                                @endif

                                <p class="text-xs text-base-content/40 mt-1">
                                    {{ $review->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- PAGINAÇÃO --}}
                @if($reviews->hasPages())
                    <div class="mt-8 flex justify-center">
                        <div class="join">
                            @if($reviews->onFirstPage())
                                <button class="join-item btn btn-sm btn-disabled">«</button>
                            @else
                                <a href="{{ $reviews->previousPageUrl() }}" class="join-item btn btn-sm">«</a>
                            @endif

                            @foreach($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                                @if($page >= $reviews->currentPage() - 2 && $page <= $reviews->currentPage() + 2)
                                    <a href="{{ $url }}" class="join-item btn btn-sm {{ $page == $reviews->currentPage() ? 'btn-active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if($reviews->hasMorePages())
                                <a href="{{ $reviews->nextPageUrl() }}" class="join-item btn btn-sm">»</a>
                            @else
                                <button class="join-item btn btn-sm btn-disabled">»</button>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12 bg-base-200 rounded-box">
                    <svg class="h-16 w-16 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <p class="text-base-content/60 text-lg">Ainda não há reviews para este livro.</p>
                    <p class="text-sm text-base-content/50">Sê o primeiro a avaliar!</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Modais de aviso --}}
    <dialog id="twofaWarningModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-warning">Autenticação de Dois Fatores Necessária</h3>
            <div class="py-4">
                <p>Para poderes requisitar livros, precisas de ativar o 2FA na tua conta.</p>
            </div>
            <div class="modal-action">
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
                <a href="{{ route('profile.index') }}" class="btn btn-warning">Ativar 2FA</a>
            </div>
        </div>
    </dialog>

    <dialog id="twofaHistoricoModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-warning">Autenticação de Dois Fatores Necessária</h3>
            <div class="py-4">
                <p>Para veres o histórico, precisas de ativar o 2FA na tua conta.</p>
            </div>
            <div class="modal-action">
                <form method="dialog"><button class="btn btn-ghost">Cancelar</button></form>
                <a href="{{ route('profile.index') }}" class="btn btn-warning">Ativar 2FA</a>
            </div>
        </div>
    </dialog>

    <dialog id="loginRequisitarModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-info">Acesso Exclusivo</h3>
            <div class="py-4">
                <p>Para requisitar livros, precisas de ter uma conta e ativar 2FA.</p>
            </div>
            <div class="modal-action">
                <form method="dialog"><button class="btn btn-ghost">Agora não</button></form>
                <div class="space-x-2">
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Registar</a>
                </div>
            </div>
        </div>
    </dialog>
</x-layouts.layout>
