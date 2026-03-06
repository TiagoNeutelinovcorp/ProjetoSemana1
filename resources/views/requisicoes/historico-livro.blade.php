<x-layouts.layout title="Histórico do Livro">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold">Histórico de Requisições</h1>
                <p class="text-base-content/70 mt-2">
                    Livro: <span class="font-bold text-info">{{ $livro->nome }}</span>
                </p>
                <p class="text-sm text-base-content/60">
                    ISBN: {{ $livro->isbn }} | Editora: {{ $livro->editora->nome }}
                </p>
            </div>
            <a href="{{ route('livros.show', $livro) }}" class="btn btn-ghost">
                ← Voltar ao Livro
            </a>
        </div>

        {{-- Estatísticas rápidas --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-info text-sm">Total Requisições</h2>
                    <p class="text-3xl font-bold">{{ $estatisticas['total'] }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-warning text-sm">Ativas</h2>
                    <p class="text-3xl font-bold">{{ $estatisticas['ativas'] }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-success text-sm">Concluídas</h2>
                    <p class="text-3xl font-bold">{{ $estatisticas['concluidas'] }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-error text-sm">Atrasadas</h2>
                    <p class="text-3xl font-bold">{{ $estatisticas['atrasadas'] }}</p>
                </div>
            </div>
        </div>

        {{-- Tabela de histórico --}}
        <div class="overflow-x-auto bg-base-100 rounded-box shadow-xl">
            <table class="table">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Nome</th>
                    <th>Data Requisição</th>
                    <th>Data Prevista</th>
                    <th>Data Devolução</th>
                    <th>Status</th>
                    <th>Dias Atraso</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requisicoes as $req)
                    <tr>
                        <td class="font-mono">{{ $req->codigo }}</td>
                        <td>
                            <div class="font-bold">{{ $req->user->name }}</div>
                            <div class="text-xs text-base-content/70">{{ $req->user->email }}</div>
                        </td>
                        <td>{{ $req->data_requisicao->format('d/m/Y') }}</td>
                        <td>{{ $req->data_prevista_devolucao->format('d/m/Y') }}</td>
                        <td>
                            @if($req->data_devolucao_real)
                                {{ $req->data_devolucao_real->format('d/m/Y') }}
                            @else
                                <span class="text-base-content/50">-</span>
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
                            <span class="badge {{ $statusClasses[$req->status] ?? 'badge-ghost' }}">
                                    {{ $statusTexts[$req->status] ?? $req->status }}
                                </span>
                        </td>
                        <td>
                            @if($req->dias_atraso > 0)
                                <span class="text-error font-bold">{{ $req->dias_atraso }} dias</span>
                            @else
                                <span class="text-base-content/50">0</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('requisicoes.show', $req) }}" class="btn btn-xs btn-info">
                                Ver Detalhes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-12 w-12 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-base-content/60 text-lg">Nenhuma requisição encontrada para este livro</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação --}}
        @if($requisicoes->hasPages())
            <div class="mt-6 flex justify-center">
                <div class="join">
                    {{-- Botão Anterior --}}
                    @if($requisicoes->onFirstPage())
                        <button class="join-item btn btn-disabled">«</button>
                    @else
                        <a href="{{ $requisicoes->previousPageUrl() }}" class="join-item btn">«</a>
                    @endif

                    {{-- Números das páginas (limitado a 10 páginas) --}}
                    @php
                        $start = max(1, $requisicoes->currentPage() - 5);
                        $end = min($requisicoes->lastPage(), $requisicoes->currentPage() + 4);
                    @endphp

                    @for($page = $start; $page <= $end; $page++)
                        <a href="{{ $requisicoes->url($page) }}"
                           class="join-item btn {{ $page == $requisicoes->currentPage() ? 'btn-active' : '' }}">
                            {{ $page }}
                        </a>
                    @endfor

                    {{-- Botão Próximo --}}
                    @if($requisicoes->hasMorePages())
                        <a href="{{ $requisicoes->nextPageUrl() }}" class="join-item btn">»</a>
                    @else
                        <button class="join-item btn btn-disabled">»</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts.layout>
