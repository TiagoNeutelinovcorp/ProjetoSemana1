<x-layouts.layout title="Autores">
    <div class="max-w-7xl mx-auto">
        {{-- LINHA SUPERIOR COM TÍTULO, PESQUISA E BOTÃO --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
            {{-- Título à esquerda --}}
            <h1 class="text-3xl font-bold">Autores</h1>

            {{-- Barra de pesquisa --}}
            <div class="w-full md:w-auto flex-1 max-w-2xl md:ml-4 lg:ml-8">
                <form method="GET" action="{{ route('autores.index') }}">
                    <label class="input input-bordered flex items-center gap-2">
                        <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </g>
                        </svg>
                        <input type="search" name="pesquisa" value="{{ request('pesquisa') }}" class="grow" placeholder="Pesquisar autor por nome..." />
                        <button type="submit" class="btn btn-sm btn-info">Pesquisar</button>
                        @if(request('pesquisa'))
                            <a href="{{ route('autores.index') }}" class="btn btn-sm btn-ghost">Limpar</a>
                        @endif
                    </label>
                </form>
            </div>

            {{-- Botão Novo Autor à direita --}}
            @auth
                @if(auth()->user()->isBibliotecario())
                    <a href="{{ route('autores.create') }}" class="btn btn-info">
                        Novo Autor
                    </a>
                @endif
            @endauth
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6 shadow-lg">
                <span>{{ session('sucesso') }}</span>
            </div>
        @endif

        @if(session('erro'))
            <div class="alert alert-error mb-6 shadow-lg">
                <span>{{ session('erro') }}</span>
            </div>
        @endif

        {{-- TABELA DE AUTORES --}}
        <div class="overflow-x-auto bg-base-100 rounded-box shadow-xl">
            <table class="table">
                {{-- HEAD --}}
                <thead>
                <tr>
                    <th>Autor</th>
                    <th>Livros Publicados</th>
                    <th>Ações</th>
                </tr>
                </thead>

                {{-- BODY --}}
                <tbody>
                @forelse($autores as $autor)
                    <tr>
                        {{-- Autor com avatar --}}
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="mask mask-squircle h-12 w-12">
                                        <img
                                            src="{{ $autor->foto_url }}"
                                            alt="{{ $autor->nome }}"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold">{{ $autor->nome }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Livros publicados --}}
                        <td>
                            {{ $autor->livros->count() }} livro(s)
                            <br />
                        </td>

                        {{-- Ações --}}
                        <td>
                            <div class="flex gap-2">
                                {{-- Botão VER LIVROS --}}
                                <a href="{{ route('livros.index', ['autor' => $autor->id]) }}"
                                   class="btn btn-sm btn-info">
                                    Ver Livros
                                </a>

                                @if(auth()->user()?->isBibliotecario())
                                    <a href="{{ route('autores.edit', $autor) }}"
                                       class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                    <form action="{{ route('autores.destroy', $autor) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-error"
                                                onclick="return confirm('Tem a certeza que deseja apagar este autor?')">
                                            Apagar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-8">
                            <p class="text-base-content/60">
                                @if(request('pesquisa'))
                                    Nenhum autor encontrado para "{{ request('pesquisa') }}"
                                @else
                                    Nenhum autor encontrado
                                @endif
                            </p>
                            @auth
                                @if(auth()->user()->isBibliotecario())
                                    <a href="{{ route('autores.create') }}" class="btn btn-info mt-4">
                                        Adicionar o primeiro autor
                                    </a>
                                @endif
                            @endauth
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINAÇÃO --}}
        @if($autores->hasPages())
            <div class="mt-8 flex justify-center">
                <div class="join">
                    @if($autores->onFirstPage())
                        <button class="join-item btn btn-disabled">«</button>
                    @else
                        <a href="{{ $autores->previousPageUrl() }}" class="join-item btn">«</a>
                    @endif

                    @foreach($autores->getUrlRange(1, $autores->lastPage()) as $page => $url)
                        <a href="{{ $url }}"
                           class="join-item btn {{ $page == $autores->currentPage() ? 'btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    @if($autores->hasMorePages())
                        <a href="{{ $autores->nextPageUrl() }}" class="join-item btn">»</a>
                    @else
                        <button class="join-item btn btn-disabled">»</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.layout>
