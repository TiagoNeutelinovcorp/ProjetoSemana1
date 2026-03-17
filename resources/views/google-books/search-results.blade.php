<x-layouts.layout title="Resultados da Pesquisa">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold">Resultados para "{{ $query }}"</h1>
                @if(isset($totalItems))
                    <p class="text-base-content/70 mt-1">{{ $totalItems }} livros encontrados | Página {{ $page }} de {{ $totalPages }}</p>
                @endif
            </div>
            <a href="{{ route('google-books.search.form') }}" class="btn btn-ghost">
                ← Nova Pesquisa
            </a>
        </div>

        @if(session('erro'))
            <div class="alert alert-error mb-6">{{ session('erro') }}</div>
        @endif

        @if(session('aviso'))
            <div class="alert alert-warning mb-6">{{ session('aviso') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-4">
            @forelse($books as $index => $book)
                <div class="card card-side bg-base-100 shadow-lg hover:shadow-xl transition-all duration-300">
                    {{-- Capa do livro --}}
                    <figure class="w-24 h-auto flex-shrink-0 bg-base-200">
                        @if($book['capa_thumbnail'])
                            <img src="{{ $book['capa_thumbnail'] }}"
                                 alt="{{ $book['titulo'] }}"
                                 class="object-cover w-full h-full">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-base-content/30">
                                <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        @endif
                    </figure>

                    {{-- Informações do livro --}}
                    <div class="card-body">
                        <h2 class="card-title text-lg font-bold line-clamp-2">{{ $book['titulo'] }}</h2>

                        <div class="text-sm space-y-1">
                            <p class="flex items-start gap-2">
                                <span class="font-bold w-16">Autores:</span>
                                <span>{{ implode(', ', $book['autores']) }}</span>
                            </p>
                            <p class="flex items-start gap-2">
                                <span class="font-bold w-16">Editora:</span>
                                <span>{{ $book['editora'] }}</span>
                            </p>
                            <p class="flex items-start gap-2">
                                <span class="font-bold w-16">ISBN:</span>
                                <span class="font-mono">{{ $book['isbn'] ?? 'N/A' }}</span>
                            </p>

                            {{-- PREÇO --}}
                            @if(isset($book['preco']) && $book['preco'] > 0)
                                <p class="flex items-start gap-2 mt-2">
                                    <span class="font-bold w-16">Preço:</span>
                                    <span class="text-success font-bold">
                                        {{ $book['moeda'] ?? 'EUR' }} {{ number_format($book['preco'], 2, ',', '.') }}
                                    </span>
                                </p>
                            @else
                                <p class="flex items-start gap-2 mt-2">
                                    <span class="font-bold w-16">Preço:</span>
                                    <span class="text-base-content/50">Não disponível</span>
                                </p>
                            @endif
                        </div>

                        <div class="card-actions justify-end mt-2">
                            @auth
                                @if(auth()->user()->isBibliotecario())
                                    {{-- ADMIN: importação direta --}}
                                    <form action="{{ route('google-books.import') }}" method="POST"
                                          onsubmit="return confirm('Tens a certeza que desejas importar este livro?')">
                                        @csrf
                                        <input type="hidden" name="titulo" value="{{ $book['titulo'] }}">
                                        <input type="hidden" name="autores" value="{{ implode(',', $book['autores']) }}">
                                        <input type="hidden" name="editora" value="{{ $book['editora'] }}">
                                        <input type="hidden" name="descricao" value="{{ $book['descricao'] }}">
                                        <input type="hidden" name="isbn" value="{{ $book['isbn'] }}">
                                        <input type="hidden" name="capa_thumbnail" value="{{ $book['capa_thumbnail'] }}">
                                        <input type="hidden" name="capa_grande" value="{{ $book['capa_grande'] }}">
                                        <input type="hidden" name="paginas" value="{{ $book['paginas'] }}">
                                        <input type="hidden" name="data_publicacao" value="{{ $book['data_publicacao'] }}">
                                        <input type="hidden" name="preco" value="{{ $book['preco'] ?? 0 }}">

                                        <button type="submit" class="btn btn-xs btn-success">
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            Importar
                                        </button>
                                    </form>
                                @else
                                    {{-- CLIENTE: sugerir livro com modal --}}
                                    <button class="btn btn-xs btn-warning" onclick="sugerirModal{{ $index }}.showModal()">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Sugerir Livro
                                    </button>

                                    {{-- Modal de sugestão --}}
                                    <dialog id="sugerirModal{{ $index }}" class="modal modal-bottom sm:modal-middle">
                                        <div class="modal-box">
                                            <h3 class="text-lg font-bold text-warning">Sugerir Livro</h3>
                                            <div class="py-4">
                                                <p>Confirmas que desejas sugerir o livro</p>
                                                <p class="font-bold text-lg my-2">"{{ $book['titulo'] }}"</p>

                                                @if(isset($book['preco']) && $book['preco'] > 0)
                                                    <p class="text-sm mt-2">
                                                        <span class="font-bold">Preço:</span>
                                                        <span class="text-success">{{ $book['moeda'] ?? 'EUR' }} {{ number_format($book['preco'], 2, ',', '.') }}</span>
                                                    </p>
                                                @endif

                                                <p class="text-sm text-base-content/70 mt-4">Após sugerires, um administrador irá avaliar e aprovar a inclusão do livro na biblioteca.</p>
                                            </div>
                                            <div class="modal-action">
                                                <form method="dialog">
                                                    <button class="btn btn-ghost">Cancelar</button>
                                                </form>
                                                <form action="{{ route('google-books.sugerir') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="titulo" value="{{ $book['titulo'] }}">
                                                    <input type="hidden" name="autores" value="{{ implode(',', $book['autores']) }}">
                                                    <input type="hidden" name="editora" value="{{ $book['editora'] }}">
                                                    <input type="hidden" name="descricao" value="{{ $book['descricao'] }}">
                                                    <input type="hidden" name="isbn" value="{{ $book['isbn'] }}">
                                                    <input type="hidden" name="capa_thumbnail" value="{{ $book['capa_thumbnail'] }}">
                                                    <input type="hidden" name="capa_grande" value="{{ $book['capa_grande'] }}">
                                                    <input type="hidden" name="paginas" value="{{ $book['paginas'] }}">
                                                    <input type="hidden" name="data_publicacao" value="{{ $book['data_publicacao'] }}">
                                                    <input type="hidden" name="preco" value="{{ $book['preco'] ?? 0 }}">
                                                    <button type="submit" class="btn btn-warning">
                                                        Confirmar Sugestão
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </dialog>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 bg-base-200 rounded-box">
                    <svg class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="text-base-content/60 text-xl mb-4">Nenhum livro encontrado</p>
                    <a href="{{ route('google-books.search.form') }}" class="btn btn-info">Nova Pesquisa</a>
                </div>
            @endforelse
        </div>

        {{-- PAGINAÇÃO --}}
        @if(isset($totalPages) && $totalPages > 1)
            <div class="mt-8 flex justify-center">
                <div class="join">
                    {{-- Botão Anterior --}}
                    @if($page > 1)
                        <a href="{{ route('google-books.search', ['query' => $query, 'page' => $page - 1]) }}" class="join-item btn">«</a>
                    @else
                        <button class="join-item btn btn-disabled">«</button>
                    @endif

                    {{-- Números das páginas (limitado a 5 páginas à volta da atual) --}}
                    @php
                        $start = max(1, $page - 2);
                        $end = min($totalPages, $page + 2);
                    @endphp

                    @for($i = $start; $i <= $end; $i++)
                        <a href="{{ route('google-books.search', ['query' => $query, 'page' => $i]) }}"
                           class="join-item btn {{ $i == $page ? 'btn-active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    {{-- Botão Próximo --}}
                    @if($page < $totalPages)
                        <a href="{{ route('google-books.search', ['query' => $query, 'page' => $page + 1]) }}" class="join-item btn">»</a>
                    @else
                        <button class="join-item btn btn-disabled">»</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.layout>
