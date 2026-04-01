<x-layouts.layout title="Requisições">
    <div class="max-w-7xl mx-auto">
        {{-- Cabeçalho --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Requisições</h1>
            <p class="text-base-content/70 mt-1">Gestão de todas as requisições da biblioteca</p>
        </div>

        {{-- Cards de Estatísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-info">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Requisições Ativas</div>
                <div class="stat-value text-3xl text-info font-bold">{{ $estatisticas['ativas'] }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-warning">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Últimos 30 Dias</div>
                <div class="stat-value text-3xl text-warning font-bold">{{ $estatisticas['ultimos_30_dias'] }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-success">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Entregues Hoje</div>
                <div class="stat-value text-3xl text-success font-bold">{{ $estatisticas['entregues_hoje'] }}</div>
            </div>
        </div>

        {{-- Filtros Compactos --}}
        <div class="bg-base-200 rounded-box shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="form-control w-40">
                    <label class="label">
                        <span class="label-text text-xs">Status</span>
                    </label>
                    <select name="status" class="select select-bordered select-sm">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="devolucao_solicitada" {{ request('status') == 'devolucao_solicitada' ? 'selected' : '' }}>Devolução Solicitada</option>
                        <option value="concluido" {{ request('status') == 'concluido' ? 'selected' : '' }}>Concluído</option>
                        <option value="atrasado" {{ request('status') == 'atrasado' ? 'selected' : '' }}>Atrasado</option>
                    </select>
                </div>

                <div class="form-control w-52">
                    <label class="label">
                        <span class="label-text text-xs">Cidadão</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="cidadao"
                               value="{{ request('cidadao') }}"
                               placeholder="Nome do cidadão..."
                               class="input input-bordered input-sm w-full pl-8">
                        <svg class="absolute left-2 top-1/2 -translate-y-1/2 h-4 w-4 text-base-content/50"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>

                <div class="form-control w-52">
                    <label class="label">
                        <span class="label-text text-xs">Livro</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="livro"
                               value="{{ request('livro') }}"
                               placeholder="Título do livro..."
                               class="input input-bordered input-sm w-full pl-8">
                        <svg class="absolute left-2 top-1/2 -translate-y-1/2 h-4 w-4 text-base-content/50"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['status', 'cidadao', 'livro']))
                        <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost btn-sm">
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
                        <th class="text-xs uppercase tracking-wider">Cidadão</th>
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
                            <td>
                                <div class="font-medium">{{ $requisicao->user?->name ?? 'Utilizador não encontrado' }}</div>
                                <div class="text-xs text-base-content/50">{{ $requisicao->user?->email ?? '-' }}</div>
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
                                    $statusConfig = [
                                        'pendente' => ['label' => 'Pendente', 'class' => 'badge-warning'],
                                        'ativo' => ['label' => 'Ativo', 'class' => 'badge-info'],
                                        'devolucao_solicitada' => ['label' => 'Devolução Solicitada', 'class' => 'badge-warning'],
                                        'concluido' => ['label' => 'Concluído', 'class' => 'badge-success'],
                                        'atrasado' => ['label' => 'Atrasado', 'class' => 'badge-error'],
                                    ];
                                    $status = $statusConfig[$req->status] ?? ['label' => $req->status, 'class' => 'badge-ghost'];
                                @endphp
                                <span class="badge {{ $status['class'] }} badge-sm">{{ $status['label'] }}</span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <a href="{{ route('requisicoes.show', $req) }}" class="btn btn-xs btn-info">
                                        Ver
                                    </a>

                                    @if(auth()->user()->isBibliotecario() && in_array($req->status, ['ativo', 'devolucao_solicitada']))
                                        <button class="btn btn-xs btn-success" onclick="devolucaoModal{{ $req->id }}.showModal()">
                                            Confirmar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
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

            {{-- MODAIS DE CONFIRMAÇÃO DE DEVOLUÇÃO --}}
            @foreach($requisicoes as $req)
                @if(auth()->user()->isBibliotecario() && in_array($req->status, ['ativo', 'devolucao_solicitada']))
                    <dialog id="devolucaoModal{{ $req->id }}" class="modal modal-bottom sm:modal-middle">
                        <div class="modal-box">
                            <h3 class="text-lg font-bold text-success">Confirmar Devolução</h3>
                            <div class="py-4">
                                <p>Tens a certeza que desejas confirmar a devolução do livro
                                    <span class="font-bold">"{{ $req->livro->nome }}"</span>?</p>
                                @if($req->isAtrasada())
                                    <div class="alert alert-warning mt-4">
                                        <span>Este livro está com {{ $req->calcularDiasAtraso() }} dias de atraso!</span>
                                    </div>
                                @endif
                                <p class="text-sm text-base-content/70 mt-4">
                                    Esta ação registará a data de devolução e encerrará a requisição.
                                </p>
                            </div>
                            <div class="modal-action">
                                <form method="dialog">
                                    <button class="btn btn-ghost">Cancelar</button>
                                </form>
                                <form action="{{ route('requisicoes.confirmar-devolucao', $req) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success">
                                        Confirmar Devolução
                                    </button>
                                </form>
                            </div>
                        </div>
                    </dialog>
                @endif
            @endforeach

            {{-- Rodapé da tabela com contagem e paginação centralizada --}}
            <div class="bg-base-200 px-4 py-3 flex flex-col items-center gap-3 text-sm">
                <span class="text-base-content/70">
                    Mostrando {{ $requisicoes->firstItem() ?? 0 }} - {{ $requisicoes->lastItem() ?? 0 }} de {{ $requisicoes->total() }} resultados
                </span>

                @if($requisicoes->hasPages())
                    <div class="join">
                        <a href="{{ $requisicoes->previousPageUrl() }}"
                           class="join-item btn btn-sm {{ $requisicoes->onFirstPage() ? 'btn-disabled' : '' }}">
                            «
                        </a>

                        @foreach(range(1, $requisicoes->lastPage()) as $page)
                            @if($page >= $requisicoes->currentPage() - 2 && $page <= $requisicoes->currentPage() + 2)
                                <a href="{{ $requisicoes->url($page) }}"
                                   class="join-item btn btn-sm {{ $page == $requisicoes->currentPage() ? 'btn-active' : '' }}">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        <a href="{{ $requisicoes->nextPageUrl() }}"
                           class="join-item btn btn-sm {{ !$requisicoes->hasMorePages() ? 'btn-disabled' : '' }}">
                            »
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.layout>
