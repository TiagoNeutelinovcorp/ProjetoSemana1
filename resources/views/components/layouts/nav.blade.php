<nav class="bg-base-300 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            {{-- HOME --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="group">
                    <span class="text-base-content font-bold text-xl group-hover:text-info transition">HOME</span>
                </a>
            </div>

            {{-- MENU CENTRAL (SEM BOTÕES AMARELOS) --}}
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
            </div>

            {{-- ÁREA DO UTILIZADOR --}}
            <div class="flex items-center space-x-4">
                @auth
                    <div class="flex items-center space-x-3 bg-base-200 rounded-full pl-4 pr-4 py-1 border border-base-100">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-base-content">{{ auth()->user()->name }}</span>
                            <span class="text-xs font-bold
                                @if(auth()->user()->isBibliotecario()) text-warning
                                @else text-info
                                @endif">
                                {{ strtoupper(auth()->user()->role) }}
                            </span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="p-1 rounded-full hover:bg-error/20 transition" title="Sair">
                                <svg class="h-5 w-5 text-base-content/50 group-hover:text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm text-black">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary btn-sm text-black">Registar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
