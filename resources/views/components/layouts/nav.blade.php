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
