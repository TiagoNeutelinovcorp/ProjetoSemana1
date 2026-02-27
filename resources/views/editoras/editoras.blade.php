<x-layouts.layout title="Editoras">
    <div class="max-w-7xl mx-auto">
        {{-- LINHA SUPERIOR COM TÍTULO, PESQUISA E BOTÃO --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-8">
            {{-- Título à esquerda --}}
            <h1 class="text-3xl font-bold">Editoras</h1>

            {{-- Barra de pesquisa mais larga e deslocada --}}
            <div class="w-full md:w-auto flex-1 max-w-2xl md:ml-4 lg:ml-8">
                <form method="GET" action="{{ route('editoras.index') }}">
                    <label class="input input-bordered flex items-center gap-2">
                        <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </g>
                        </svg>
                        <input type="search" name="pesquisa" value="{{ request('pesquisa') }}" class="grow" placeholder="Pesquisar editora por nome..." />
                        <button type="submit" class="btn btn-sm btn-info">Pesquisar</button>
                        @if(request('pesquisa'))
                            <a href="{{ route('editoras.index') }}" class="btn btn-sm btn-ghost">Limpar</a>
                        @endif
                    </label>
                </form>
            </div>

            {{-- Botão Nova Editora à direita --}}
            @auth
                @if(auth()->user()->isBibliotecario())
                    <a href="{{ route('editoras.create') }}" class="btn btn-info">
                        Nova Editora
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

        {{-- TABELA DE EDITORAS --}}
        <div class="overflow-x-auto bg-base-100 rounded-box shadow-xl">
            <table class="table">
                {{-- HEAD --}}
                <thead>
                <tr>
                    <th>Editora</th>
                    <th>Livros Publicados</th>
                    <th>Ações</th>
                </tr>
                </thead>

                {{-- BODY --}}
                <tbody>
                @forelse($editoras as $editora)
                    <tr>
                        {{-- Editora com logotipo --}}
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="avatar">
                                    <div class="mask mask-squircle h-12 w-12 bg-base-300 flex items-center justify-center">
                                        @if($editora->logotipo)
                                            <img
                                                src="{{ asset('storage/' . $editora->logotipo) }}"
                                                alt="{{ $editora->nome }}"
                                                class="w-full h-full object-cover"
                                            />
                                        @else
                                            <span class="text-lg font-bold">{{ substr($editora->nome, 0, 1) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold">{{ $editora->nome }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Livros publicados --}}
                        <td>
                            {{ $editora->livros->count() }} livro(s)
                            <br />
                        </td>

                        {{-- Ações --}}
                        <td>
                            <div class="flex gap-2">
                                {{-- Botão VER LIVROS (filtra por esta editora) --}}
                                <a href="{{ route('livros.index', ['editora' => $editora->id]) }}"
                                   class="btn btn-sm btn-info">
                                    Ver Livros
                                </a>

                                @if(auth()->user()?->isBibliotecario())
                                    <a href="{{ route('editoras.edit', $editora) }}"
                                       class="btn btn-sm btn-warning">
                                        Editar
                                    </a>
                                    <form action="{{ route('editoras.destroy', $editora) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-error"
                                                onclick="return confirm('Tem a certeza que deseja apagar esta editora?')">
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
                                    Nenhuma editora encontrada para "{{ request('pesquisa') }}"
                                @else
                                    Nenhuma editora encontrada
                                @endif
                            </p>
                            @auth
                                @if(auth()->user()->isBibliotecario())
                                    <a href="{{ route('editoras.create') }}" class="btn btn-info mt-4">
                                        Adicionar a primeira editora
                                    </a>
                                @endif
                            @endauth
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINAÇÃO COM DAISYUI --}}
        @if($editoras->hasPages())
            <div class="mt-8 flex justify-center">
                <div class="join">
                    {{-- Botão Anterior --}}
                    @if($editoras->onFirstPage())
                        <button class="join-item btn btn-disabled">«</button>
                    @else
                        <a href="{{ $editoras->previousPageUrl() }}" class="join-item btn">«</a>
                    @endif

                    {{-- Números das páginas --}}
                    @foreach($editoras->getUrlRange(1, $editoras->lastPage()) as $page => $url)
                        <a href="{{ $url }}"
                           class="join-item btn {{ $page == $editoras->currentPage() ? 'btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Botão Próximo --}}
                    @if($editoras->hasMorePages())
                        <a href="{{ $editoras->nextPageUrl() }}" class="join-item btn">»</a>
                    @else
                        <button class="join-item btn btn-disabled">»</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.layout>

