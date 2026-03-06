<x-layouts.layout title="Detalhes da Requisição">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Requisição {{ $requisicao->codigo }}</h1>
            <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost">
                Voltar
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <div class="card bg-base-100 shadow-xl mb-6">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Informações da Requisição</h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-base-content/70">Código</p>
                                <p class="font-bold font-mono">{{ $requisicao->codigo }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-base-content/70">Status</p>
                                @php
                                    $statusClasses = [
                                        'pendente' => 'badge-warning',
                                        'ativo' => 'badge-info',
                                        'concluido' => 'badge-success',
                                        'atrasado' => 'badge-error',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$requisicao->status] ?? 'badge-ghost' }} badge-lg">
                                    {{ ucfirst($requisicao->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-base-content/70">Data da Requisição</p>
                                <p class="font-bold">{{ $requisicao->data_requisicao->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-base-content/70">Data Prevista Devolução</p>
                                <p class="font-bold">{{ $requisicao->data_prevista_devolucao->format('d/m/Y') }}</p>
                            </div>
                            @if($requisicao->data_devolucao_real)
                                <div>
                                    <p class="text-sm text-base-content/70">Data Devolução Real</p>
                                    <p class="font-bold">{{ $requisicao->data_devolucao_real->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                            @if($requisicao->dias_atraso > 0)
                                <div>
                                    <p class="text-sm text-base-content/70">Dias em Atraso</p>
                                    <p class="font-bold text-error">{{ $requisicao->dias_atraso }} dias</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Livro</h2>

                        <div class="flex gap-6">
                            <div class="w-32 h-40">
                                <img src="{{ $requisicao->livro->imagem_capa_url }}" alt="{{ $requisicao->livro->nome }}"
                                     class="w-full h-full object-cover rounded">
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold">{{ $requisicao->livro->nome }}</h3>
                                <p class="text-base-content/70">ISBN: {{ $requisicao->livro->isbn }}</p>
                                <p class="text-base-content/70">Editora: {{ $requisicao->livro->editora->nome }}</p>
                                <p class="text-lg font-bold text-info mt-2">{{ $requisicao->livro->preco_formatado }}</p>

                                <div class="mt-4">
                                    <h4 class="font-bold mb-2">Autores:</h4>
                                    <ul class="list-disc list-inside">
                                        @foreach($requisicao->livro->autores as $autor)
                                            <li>{{ $autor->nome }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cidadão --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-xl mb-4">Cidadão</h2>

                    <div class="flex items-center gap-4 mb-4">
                        <div class="avatar">
                            <div class="w-12 h-12 rounded-full ring ring-primary ring-offset-base-100 ring-offset-1">
                                @if($requisicao->user->profile_photo_url)
                                    <img src="{{ $requisicao->user->profile_photo_url }}" alt="{{ $requisicao->user->name }}" />
                                @else
                                    <div class="bg-neutral text-neutral-content text-lg flex items-center justify-center w-full h-full">
                                        {{ substr($requisicao->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="font-bold">{{ $requisicao->user->name }}</p>
                            <p class="text-sm text-base-content/70">{{ $requisicao->user->email }}</p>
                        </div>
                    </div>

                    @if(auth()->user()->isBibliotecario())
                        <a href="{{ route('requisicoes.historico', $requisicao->user) }}"
                           class="btn btn-outline btn-sm w-full">
                            Ver Histórico do Cidadão
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
