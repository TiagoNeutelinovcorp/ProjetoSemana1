<x-layouts.layout title="Livros">
    <div class="max-w-7xl mx-auto">
        {{-- Cabeçalho com título e botões --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-3xl font-bold">Livros</h1>
            <div class="flex gap-2">
                @auth
                    @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                        <a href="{{ route('livros.export') }}" class="btn btn-success">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M16 8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Exportar Excel
                        </a>
                        <a href="{{ route('livros.create') }}" class="btn btn-info">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Novo Livro
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-base-200 p-6 rounded-box mb-8 shadow-md">
            <form method="GET" action="{{ route('livros.index') }}" class="flex flex-wrap gap-4 items-end">
                {{-- Pesquisa --}}
                <div class="form-control flex-1 min-w-[250px]">
                    <label class="label">
                        <span class="label-text font-medium">Pesquisar</span>
                    </label>
                    <input type="text" name="pesquisa" value="{{ request('pesquisa') }}"
                           placeholder="Nome do livro ou ISBN"
                           class="input input-bordered w-full" />
                </div>

                {{-- Filtro por Autor --}}
                <div class="form-control w-44">
                    <label class="label">
                        <span class="label-text font-medium">Autor</span>
                    </label>
                    <select name="autor" class="select select-bordered">
                        <option value="">Todos os autores</option>
                        @foreach($autores as $autor)
                            <option value="{{ $autor->id }}" {{ request('autor') == $autor->id ? 'selected' : '' }}>
                                {{ $autor->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Editora --}}
                <div class="form-control w-44">
                    <label class="label">
                        <span class="label-text font-medium">Editora</span>
                    </label>
                    <select name="editora" class="select select-bordered">
                        <option value="">Todas as editoras</option>
                        @foreach($editoras as $editora)
                            <option value="{{ $editora->id }}" {{ request('editora') == $editora->id ? 'selected' : '' }}>
                                {{ $editora->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botões de ação --}}
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    @if(request()->anyFilled(['pesquisa', 'autor', 'editora']))
                        <a href="{{ route('livros.index') }}" class="btn btn-ghost">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Mensagem de sucesso --}}
        @if(session('sucesso'))
            <div class="alert alert-success mb-6 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('sucesso') }}</span>
            </div>
        @endif

        {{-- Grid de livros --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($livros as $livro)
                <div class="card card-side bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300 h-44">
                    {{-- Imagem --}}
                    <figure class="w-24 h-44 flex-shrink-0">
                        <img src="{{ $livro->imagem_capa_url }}" alt="{{ $livro->nome }}"
                             class="w-full h-full object-cover">
                    </figure>

                    {{-- Conteúdo --}}
                    <div class="card-body p-3">
                        <h2 class="card-title text-base font-bold line-clamp-2">{{ $livro->nome }}</h2>

                        <div class="text-xs space-y-1 mt-1">
                            <p class="flex items-center gap-1">
                                <span class="text-base-content/70">Ed:</span>
                                <span class="font-medium truncate">{{ $livro->editora->nome ?? 'N/A' }}</span>
                            </p>
                            <p class="flex items-center gap-1">
                                <span class="text-base-content/70">Au:</span>
                                <span class="font-medium truncate">
                                    @if($livro->autores->count() > 0)
                                        {{ $livro->autores->first()->nome }}
                                        @if($livro->autores->count() > 1)
                                            <span class="badge badge-xs">+{{ $livro->autores->count() - 1 }}</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </p>
                            <p class="flex items-center gap-1">
                                <span class="text-base-content/70">€:</span>
                                <span class="font-bold text-white">{{ $livro->preco_formatado }}</span>
                            </p>

                            {{-- Indicador de disponibilidade --}}
                            <p class="flex items-center gap-1 mt-1">
                                <span class="text-base-content/70">Status:</span>
                                @if($livro->isDisponivel())
                                    <span class="badge badge-success badge-xs">Disponível</span>
                                @else
                                    <span class="badge badge-error badge-xs">Indisponível</span>
                                @endif
                            </p>
                        </div>

                        {{-- Botões de ação --}}
                        <div class="card-actions justify-end mt-2">
                            <a href="{{ route('livros.show', $livro) }}" class="btn btn-xs btn-info">Ver</a>

                            {{-- Botão de requisição com diferentes estados --}}
                            @auth
                                @if(auth()->user()->two_factor_secret)
                                    {{-- Utilizador com 2FA - pode requisitar --}}
                                    @if($livro->isDisponivel())
                                        <a href="{{ route('requisicoes.create', $livro) }}" class="btn btn-xs btn-success">
                                            Requisitar
                                        </a>
                                    @else
                                        <span class="badge badge-error badge-sm">Indisponível</span>
                                    @endif
                                @else
                                    {{-- Utilizador sem 2FA - mostra modal de aviso --}}
                                    @if($livro->isDisponivel())
                                        <button class="btn btn-xs btn-success" onclick="twofaWarningModal.showModal()">
                                            Requisitar
                                        </button>
                                    @else
                                        <span class="badge badge-error badge-sm">Indisponível</span>
                                    @endif
                                @endif
                            @else
                                {{-- Visitante não autenticado - mostra modal de login --}}
                                @if($livro->isDisponivel())
                                    <button class="btn btn-xs btn-success" onclick="loginRequisitarModal.showModal()">
                                        Requisitar
                                    </button>
                                @else
                                    <span class="badge badge-error badge-sm">Indisponível</span>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                {{-- Modal de confirmação de exclusão --}}
                <input type="checkbox" id="delete-modal-{{ $livro->id }}" class="modal-toggle" />
                <div class="modal" role="dialog">
                    <div class="modal-box">
                        <h3 class="text-lg font-bold text-error">Confirmar eliminação</h3>
                        <p class="py-4">
                            Tens a certeza que desejas apagar o livro <span class="font-bold">"{{ $livro->nome }}"</span>?
                            <br>
                            <span class="text-sm text-base-content/70">Esta ação não pode ser desfeita.</span>
                        </p>
                        <div class="modal-action">
                            <label for="delete-modal-{{ $livro->id }}" class="btn btn-ghost">Cancelar</label>
                            <form action="{{ route('livros.destroy', $livro) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-error">Sim, apagar livro</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-base-200 rounded-box">
                    <svg class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="text-base-content/60 text-xl mb-4">Nenhum livro encontrado</p>
                    @auth
                        @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                            <a href="{{ route('livros.create') }}" class="btn btn-info">Adicionar o primeiro livro</a>
                        @endif
                    @endauth
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if($livros->hasPages())
            <div class="mt-12 flex justify-center">
                <div class="join">
                    {{-- Anterior --}}
                    @if($livros->onFirstPage())
                        <button class="join-item btn btn-disabled">«</button>
                    @else
                        <a href="{{ $livros->previousPageUrl() }}" class="join-item btn">«</a>
                    @endif

                    {{-- Números das páginas --}}
                    @foreach($livros->getUrlRange(1, $livros->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="join-item btn {{ $page == $livros->currentPage() ? 'btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Próximo --}}
                    @if($livros->hasMorePages())
                        <a href="{{ $livros->nextPageUrl() }}" class="join-item btn">»</a>
                    @else
                        <button class="join-item btn btn-disabled">»</button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Modal de aviso de 2FA para Requisição (utilizador logado sem 2FA) --}}
    <dialog id="twofaWarningModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-warning">Autenticação de Dois Fatores Necessária</h3>

            <div class="py-4">
                <p class="mb-4">Para poderes requisitar livros, precisas de ativar a Autenticação de Dois Fatores (2FA) na tua conta.</p>
                <p class="text-sm text-base-content/70">A 2FA adiciona uma camada extra de segurança à tua conta, protegendo os teus dados e requisições.</p>
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost">Cancelar</button>
                </form>
                <a href="{{ route('profile.index') }}" class="btn btn-warning">
                    Ir para Perfil e Ativar 2FA
                </a>
            </div>
        </div>
    </dialog>

    {{-- Modal de aviso para Login (visitante não autenticado) --}}
    <dialog id="loginRequisitarModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-info">Acesso Exclusivo para Utilizadores Registados</h3>

            <div class="py-4">
                <p class="mb-4">Para poderes requisitar livros, precisas de ter uma conta e ativar a Autenticação de Dois Fatores (2FA).</p>
                <div class="bg-base-200 p-4 rounded-box space-y-2">
                    <p class="font-medium">Passos para acederes a esta funcionalidade:</p>
                    <ol class="list-decimal list-inside text-sm space-y-1">
                        <li>Cria uma conta (gratuita)</li>
                        <li>Faz login na tua conta</li>
                        <li>Ativa o 2FA no teu perfil</li>
                        <li>Volta e requisita o teu livro!</li>
                    </ol>
                </div>
            </div>

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost">Agora não</button>
                </form>
                <div class="space-x-2">
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary">Registar</a>
                </div>
            </div>
        </div>
    </dialog>
</x-layouts.layout>
