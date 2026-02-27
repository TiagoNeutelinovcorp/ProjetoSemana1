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
                        @if(auth()->user()->isBibliotecario())
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
                                    />
                                </figure>
                                {{-- 8 divs vazias para o efeito 3D --}}
                                <div></div><div></div><div></div><div></div>
                                <div></div><div></div><div></div><div></div>
                            </div>

                            {{-- Preço em destaque para mobile --}}
                            <div class="mt-4 text-center md:hidden">
                                <span class="text-3xl font-bold text-white">{{ $livro->preco_formatado }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Coluna das Informações --}}
                    <div class="md:col-span-2 space-y-6">
                        {{-- Preço em destaque para desktop --}}
                        <div class="hidden md:block">
                            <span class="text-4xl font-bold text-white">{{ $livro->preco_formatado }}</span>
                        </div>

                        {{-- Tabela de informações --}}
                        <div class="overflow-x-auto">
                            <table class="table">
                                <tbody>
                                {{-- ISBN --}}
                                <tr>
                                    <td class="font-bold w-32">ISBN</td>
                                    <td>{{ $livro->isbn }}</td>
                                </tr>

                                {{-- Editora --}}
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

                                {{-- Autores --}}
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

                        {{-- Bibliografia / Descrição --}}
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
    </div>
</x-layouts.layout>
