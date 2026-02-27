<x-layouts.layout title="{{ $editora->nome }}">
    <div class="max-w-4xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-3xl font-bold">{{ $editora->nome }}</h1>
                    @auth
                        @if(auth()->user()->isBibliotecario())
                            <div class="space-x-2">
                                <a href="{{ route('editoras.edit', $editora) }}" class="btn btn-warning btn-sm">
                                    Editar
                                </a>
                                <form action="{{ route('editoras.destroy', $editora) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error btn-sm" onclick="return confirm('Tem a certeza que deseja apagar esta editora?')">
                                        Apagar
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- LOGOTIPO --}}
                    <div class="md:col-span-1">
                        <div class="avatar">
                            <div class="w-full rounded-xl bg-base-300 p-4">
                                @if($editora->logotipo)
                                    <img src="{{ asset('storage/' . $editora->logotipo) }}" alt="{{ $editora->nome }}" class="w-full h-auto">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center text-6xl">
                                        {{ substr($editora->nome, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- INFORMAÇÕES --}}
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <h3 class="font-bold text-lg">Nome</h3>
                            <p>{{ $editora->nome }}</p>
                        </div>

                        <div>
                            <h3 class="font-bold text-lg">Livros Publicados</h3>
                            <p>{{ $editora->livros->count() }} {{ $editora->livros->count() == 1 ? 'livro' : 'livros' }}</p>
                        </div>

                        @if($editora->livros->count() > 0)
                            <div>
                                <h3 class="font-bold text-lg">Catálogo</h3>
                                <div class="grid grid-cols-2 gap-2 mt-2">
                                    @foreach($editora->livros as $livro)
                                        <a href="{{ route('livros.show', $livro) }}" class="card bg-base-200 hover:bg-base-300 transition p-2">
                                            <p class="font-medium truncate">{{ $livro->nome }}</p>
                                            <p class="text-sm text-base-content/70">{{ $livro->preco_formatado }}</p>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-actions justify-start mt-6">
                    <a href="{{ route('editoras.index') }}" class="btn btn-ghost">
                        ← Voltar para Editoras
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>

