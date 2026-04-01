<nav class="bg-base-300 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- HOME --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="group">
                    <span class="text-base-content font-bold text-xl group-hover:text-info transition">HOME</span>
                </a>
            </div>

            {{-- MENU CENTRAL (PÚBLICO) --}}
            <div class="hidden md:flex items-center justify-center flex-1 space-x-8">
                <a href="{{ route('livros.index') }}" class="text-base-content/70 hover:text-base-content px-3 py-2 rounded-md text-sm font-medium transition hover:bg-base-200">
                    LIVROS
                </a>
                <a href="{{ route('autores.index') }}" class="text-base-content/70 hover:text-base-content px-3 py-2 rounded-md text-sm font-medium transition hover:bg-base-200">
                    AUTORES
                </a>
                <a href="{{ route('editoras.index') }}" class="text-base-content/70 hover:text-base-content px-3 py-2 rounded-md text-sm font-medium transition hover:bg-base-200">
                    EDITORAS
                </a>

                {{-- PESQUISAR NA GOOGLE BOOKS (TODOS OS UTILIZADORES AUTENTICADOS) --}}
                @auth
                    <a href="{{ route('google-books.search.form') }}" class="text-base-content/70 hover:text-base-content px-3 py-2 rounded-md text-sm font-medium transition hover:bg-base-200">
                        PESQUISAR LIVROS
                    </a>
                @endauth

                {{-- LINK REQUISIÇÕES (apenas para bibliotecários com 2FA) --}}
                @auth
                    @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                        <a href="{{ route('requisicoes.index') }}" class="text-base-content/70 hover:text-base-content px-3 py-2 rounded-md text-sm font-medium transition hover:bg-base-200">
                            REQUISIÇÕES
                        </a>
                    @endif
                @endauth
            </div>

            {{-- ÁREA DO UTILIZADOR --}}
            <div class="flex items-center space-x-4">
                @auth
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="flex items-center space-x-2 bg-base-200 rounded-full pl-2 pr-4 py-1 border border-base-100 cursor-pointer hover:bg-base-300 transition">
                            {{-- Avatar com foto --}}
                            <div class="avatar">
                                <div class="w-8 h-8 rounded-full">
                                    @if(auth()->user()->profile_photo_url)
                                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                                    @else
                                        <div class="bg-neutral text-neutral-content text-sm flex items-center justify-center w-full h-full">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-base-content">{{ auth()->user()->name }}</span>
                                <span class="text-xs font-bold
                                    @if(auth()->user()->isBibliotecario()) text-warning
                                    @else text-info
                                    @endif">
                                    {{ strtoupper(auth()->user()->role) }}
                                </span>
                            </div>
                            <svg class="h-4 w-4 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-52 p-2 shadow-lg mt-2 border border-base-200">
                            {{-- PERFIL (sempre acessível) --}}
                            <li>
                                <a href="{{ route('profile.index') }}" class="flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Meu Perfil
                                </a>
                            </li>

                            {{-- alertas --}}
                            @if(auth()->user()->two_factor_secret)
                                <li>
                                    <a href="{{ route('alertas.index') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        Alertas
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                                <li>
                                    <a href="{{ route('admin.encomendas.index') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        Encomendas
                                     </a>
                                </li>
                            @endif
                            {{-- Menu utilizador --}}
                            @if(auth()->user()->two_factor_secret)
                                <li>
                                    <a href="{{ route('carrinho.index') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Carrinho
                                        @if(auth()->user()->carrinho_count > 0)
                                            <span class="badge badge-primary badge-sm">{{ auth()->user()->carrinho_count }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('encomendas.minhas') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        Minhas Encomendas
                                    </a>
                                </li>
                            @endif

                            {{-- MINHAS REQUISIÇÕES --}}
                            @if(auth()->user()->two_factor_secret)
                                <li>
                                    <a href="{{ route('requisicoes.minhas') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Minhas Requisições
                                    </a>
                                </li>
                            @else
                                <li>
                                    <button onclick="minhasRequisicoesModal.showModal()" class="flex items-center gap-2 w-full text-left hover:bg-base-200 p-2 rounded-lg">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Minhas Requisições
                                    </button>
                                </li>
                            @endif


                            {{-- SUGESTÕES DE LIVROS (apenas para bibliotecários) --}}
                            @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                                <li>
                                    <a href="{{ route('google-books.sugestoes') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Sugestões de Livros
                                    </a>
                                </li>
                            @endif

                            {{-- GESTÃO DE UTILIZADORES (apenas bibliotecários) --}}
                            @if(auth()->user()->isBibliotecario())
                                @if(auth()->user()->two_factor_secret)
                                    <li>
                                        <a href="{{ route('users.index') }}" class="flex items-center gap-2">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            Gerir Utilizadores
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <button onclick="gerirUtilizadoresModal.showModal()" class="flex items-center gap-2 w-full text-left hover:bg-base-200 p-2 rounded-lg">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            Gerir Utilizadores
                                        </button>
                                    </li>
                                @endif
                            @endif

                            @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                                <li>
                                    <a href="{{ route('admin.reviews.index') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                        Gestão de Reviews
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
                                <li>
                                    <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Logs do Sistema
                                    </a>
                                </li>
                            @endif

                            {{-- LOGOUT --}}
                            <li>
                                <a href="{{ url('/logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="flex items-center gap-2 text-error hover:text-error-content hover:bg-error/20">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sair
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    {{-- UTILIZADOR NÃO AUTENTICADO --}}
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm text-black">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary btn-sm text-black">Registar</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Modal de aviso de 2FA para Minhas Requisições --}}
    <dialog id="minhasRequisicoesModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-warning">Autenticação de Dois Fatores Necessária</h3>

            <div class="py-4">
                <p class="mb-4">Para acederes às tuas requisições, precisas de ativar a Autenticação de Dois Fatores (2FA) na tua conta.</p>
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

    {{-- Modal de aviso de 2FA para Gerir Utilizadores --}}
    <dialog id="gerirUtilizadoresModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-warning">Autenticação de Dois Fatores Necessária</h3>

            <div class="py-4">
                <p class="mb-4">Para gerires utilizadores, precisas de ativar a Autenticação de Dois Fatores (2FA) na tua conta.</p>
                <p class="text-sm text-base-content/70">A 2FA adiciona uma camada extra de segurança à tua conta, protegendo os dados da biblioteca.</p>
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
</nav>
