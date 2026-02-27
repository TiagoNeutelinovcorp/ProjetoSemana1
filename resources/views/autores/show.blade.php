<x-layouts.layout title="{{ $autor->nome }}">
    <div class="max-w-4xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                    <h1 class="text-3xl font-bold">{{ $autor->nome }}</h1>
                    @auth
                        @if(auth()->user()->isBibliotecario())
                            <div class="space-x-2">
                                <a href="{{ route('autores.edit', $autor) }}" class="btn btn-warning btn-sm">
                                    Editar
                                </a>
                                <form action="{{ route('autores.destroy', $autor) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error btn-sm" onclick="return confirm('Tem a certeza que deseja apagar este autor?')">
                                        Apagar
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- FOTO --}}
                    <div class="md:col-span-1">
                        <div class="avatar">
                            <div class="w-full rounded-xl">
                                <img src="{{ $autor->foto_url }}" alt="{{ $autor->nome }}" class="w-full h-auto">
                            </div>
                        </div>
                    </div>

                    {{-- INFORMAÇÕES --}}
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <h3 class="font-bold text-lg">Nome</h3>
                            <p>{{ $autor->nome }}</p>
                        </div>

                        <div>
                            <h3 class="font-bold text-lg">Livros</h3>
                            <p>{{ $autor->livros->count() }} {{ $autor->livros->count() == 1 ? 'livro' : 'livros' }}</p>
                        </div>

                        @if($autor->livros->count() > 0)
                            <div>
                                <h3 class="font-bold text-lg">Lista de Livros</h3>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($autor->livros as $livro)
                                        <a href="{{ route('livros.show', $livro) }}" class="badge badge-info badge-outline p-3">
                                            {{ $livro->nome }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-actions justify-start mt-6">
                    <a href="{{ route('autores.index') }}" class="btn btn-ghost">
                        ← Voltar para Autores
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
