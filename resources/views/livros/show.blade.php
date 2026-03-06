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
                        @if(auth()->user()->isBibliotecario() && auth()->user()->two_factor_secret)
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
                                        class="w-full h-auto object-cover rounded-2xl shadow-2xl"
                                    />
                                </figure>
                                <div></div><div></div><div></div><div></div>
                                <div></div><div></div><div></div><div></div>
                            </div>

                            <div class="mt-4 text-center md:hidden">
                                <span class="text-3xl font-bold text-white">{{ $livro->preco_formatado }}</span>
                            </div>

                            <div class="mt-4 text-center">
                                @if($livro->isDisponivel())
                                    <span class="badge badge-success badge-lg">Disponível para requisição</span>
                                @else
                                    <span class="badge badge-error badge-lg">Indisponível</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Coluna das Informações --}}
                    <div class="md:col-span-2 space-y-6">
                        <div class="hidden md:block">
                            <span class="text-4xl font-bold text-white">{{ $livro->preco_formatado }}</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td class="font-bold w-32">ISBN</td>
                                    <td>{{ $livro->isbn }}</td>
                                </tr>
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

                        <div class="flex flex-wrap gap-3 mt-4">
                            @auth
                                @if(auth()->user()->two_factor_secret)
                                    @if($livro->isDisponivel())
                                        <a href="{{ route('requisicoes.create', $livro) }}" class="btn btn-success">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Requisitar Livro
                                        </a>
                                    @endif
                                @else
                                    <button class="btn btn-success" onclick="twofaWarningModal.showModal()">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Requisitar Livro
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-success" onclick="loginRequisitarModal.showModal()">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Requisitar Livro
                                </button>
                            @endauth

                            @auth
                                @if(auth()->user()->isBibliotecario())
                                    @if(auth()->user()->two_factor_secret)
                                        <a href="{{ route('livros.historico', $livro) }}" class="btn btn-info">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Ver Histórico de Requisições
                                        </a>
                                    @else
                                        <button class="btn btn-info" onclick="twofaHistoricoModal.showModal()">
                                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Ver Histórico de Requisições
                                        </button>
                                    @endif
                                @endif
                            @endauth
                        </div>

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

    {{-- Modal de aviso de 2FA para Requisição --}}
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

    {{-- Modal de aviso de 2FA para Histórico --}}
    <dialog id="twofaHistoricoModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-warning">Autenticação de Dois Fatores Necessária</h3>

            <div class="py-4">
                <p class="mb-4">Para veres o histórico de requisições, precisas de ativar a Autenticação de Dois Fatores (2FA) na tua conta.</p>
                <p class="text-sm text-base-content/70">A 2FA adiciona uma camada extra de segurança à tua conta, protegendo os dados.</p>
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

    {{-- Modal de aviso para Login (Requisitar) --}}
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
