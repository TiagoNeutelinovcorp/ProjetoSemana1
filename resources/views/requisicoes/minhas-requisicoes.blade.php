<x-layouts.layout title="Minhas Requisições">
    <div class="max-w-7xl mx-auto">
        {{-- Cabeçalho --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Minhas Requisições</h1>
            <p class="text-base-content/70 mt-1">Acompanha o estado das tuas requisições</p>
        </div>

        {{-- Cards de Estatísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-info">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Requisições Ativas</div>
                <div class="stat-value text-3xl text-info font-bold">{{ $estatisticas['ativas'] }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-warning">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Pendentes Devolução</div>
                <div class="stat-value text-3xl text-warning font-bold">{{ $estatisticas['pendentes_devolucao'] }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-success">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Concluídas</div>
                <div class="stat-value text-3xl text-success font-bold">{{ $estatisticas['concluidas'] }}</div>
            </div>
        </div>

        {{-- Filtro Compacto --}}
        <div class="bg-base-200 rounded-box shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="form-control w-40">
                    <label class="label">
                        <span class="label-text text-xs">Status</span>
                    </label>
                    <select name="status" class="select select-bordered select-sm">
                        <option value="">Todos</option>
                        <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="devolucao_solicitada" {{ request('status') == 'devolucao_solicitada' ? 'selected' : '' }}>Devolução Solicitada</option>
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrar
                    </button>
                    @if(request()->filled('status'))
                        <a href="{{ route('requisicoes.minhas') }}" class="btn btn-ghost btn-sm">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabela de Requisições --}}
        <div class="bg-base-100 rounded-box shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead class="bg-base-200">
                    <tr>
                        <th class="text-xs uppercase tracking-wider">Código</th>
                        <th class="text-xs uppercase tracking-wider">Livro</th>
                        <th class="text-xs uppercase tracking-wider">Data Req.</th>
                        <th class="text-xs uppercase tracking-wider">Data Prev.</th>
                        <th class="text-xs uppercase tracking-wider">Status</th>
                        <th class="text-xs uppercase tracking-wider text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($requisicoes as $req)
                        <tr class="hover">
                            <td class="font-mono text-sm">{{ $req->codigo }}</td>
                            <td>
                                <div class="font-medium">{{ $req->livro->nome }}</div>
                                <div class="text-xs text-base-content/50">ISBN: {{ $req->livro->isbn }}</div>
                            </td>
                            <td class="text-sm">{{ $req->data_requisicao->format('d/m/Y') }}</td>
                            <td class="text-sm">
                                <div>{{ $req->data_prevista_devolucao->format('d/m/Y') }}</div>
                                @if($req->isAtrasada())
                                    <span class="badge badge-error badge-xs mt-1">
                                            Atrasado {{ $req->calcularDiasAtraso() }} dias
                                        </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'ativo' => 'badge-info',
                                        'devolucao_solicitada' => 'badge-warning',
                                        'concluido' => 'badge-success',
                                        'atrasado' => 'badge-error',
                                    ];
                                    $statusTexts = [
                                        'ativo' => 'Ativo',
                                        'devolucao_solicitada' => 'Devolução Solicitada',
                                        'concluido' => 'Concluído',
                                        'atrasado' => 'Atrasado',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$req->status] ?? 'badge-ghost' }} badge-sm">
                                        {{ $statusTexts[$req->status] ?? ucfirst($req->status) }}
                                    </span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('requisicoes.show', $req) }}" class="btn btn-xs btn-info">
                                        Ver
                                    </a>

                                    @if(in_array($req->status, ['ativo']))
                                        <button class="btn btn-xs btn-warning" onclick="devolucaoModal{{ $req->id }}.showModal()">
                                            Solicitar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-16 w-16 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-base-content/60 text-lg">Nenhuma requisição encontrada</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Rodapé da tabela --}}
            <div class="bg-base-200 px-4 py-3 flex flex-col items-center gap-3 text-sm">

                @if($requisicoes->hasPages())
                    <div class="join">
                        <a href="{{ $requisicoes->previousPageUrl() }}"
                           class="join-item btn btn-sm {{ $requisicoes->onFirstPage() ? 'btn-disabled' : '' }}">
                            «
                        </a>

                        @php
                            $start = max(1, $requisicoes->currentPage() - 2);
                            $end = min($requisicoes->lastPage(), $requisicoes->currentPage() + 2);
                        @endphp

                        @for($page = $start; $page <= $end; $page++)
                            <a href="{{ $requisicoes->url($page) }}"
                               class="join-item btn btn-sm {{ $page == $requisicoes->currentPage() ? 'btn-active' : '' }}">
                                {{ $page }}
                            </a>
                        @endfor

                        <a href="{{ $requisicoes->nextPageUrl() }}"
                           class="join-item btn btn-sm {{ !$requisicoes->hasMorePages() ? 'btn-disabled' : '' }}">
                            »
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modais de solicitação de devolução --}}
    @foreach($requisicoes as $req)
        @if(in_array($req->status, ['ativo']))
            <dialog id="devolucaoModal{{ $req->id }}" class="modal modal-bottom sm:modal-middle">
                <div class="modal-box">
                    <h3 class="text-lg font-bold text-warning">Solicitar Devolução</h3>

                    <div class="py-4">
                        <p>Confirmas que desejas devolver o livro
                            <span class="font-bold">"{{ $req->livro->nome }}"</span>?</p>

                        @if($req->isAtrasada())
                            <div class="alert alert-error mt-4">
                                <span>Este livro está com {{ $req->calcularDiasAtraso() }} dias de atraso!</span>
                            </div>
                        @endif

                        <p class="text-sm text-base-content/70 mt-4">
                            Após solicitares, o bibliotecário confirmará a recepção do livro.
                        </p>
                    </div>

                    <div class="modal-action">
                        <form method="dialog">
                            <button class="btn btn-ghost">Cancelar</button>
                        </form>

                        <form action="{{ route('requisicoes.solicitar-devolucao', $req) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                Confirmar Solicitação
                            </button>
                        </form>
                    </div>
                </div>
            </dialog>
        @endif
    @endforeach
</x-layouts.layout>
