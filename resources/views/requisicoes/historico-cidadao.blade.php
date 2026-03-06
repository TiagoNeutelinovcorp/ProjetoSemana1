<x-layouts.layout title="Histórico de Requisições">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Histórico de {{ $user->name }}</h1>
            <a href="{{ route('requisicoes.index') }}" class="btn btn-ghost">
                Voltar
            </a>
        </div>

        <div class="overflow-x-auto bg-base-100 rounded-box shadow-xl">
            <table class="table">
                <thead>
                <tr>
                    <th>Código</th>
                    <th>Livro</th>
                    <th>Data Requisição</th>
                    <th>Data Prevista</th>
                    <th>Data Devolução</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requisicoes as $req)
                    <tr>
                        <td class="font-mono">{{ $req->codigo }}</td>
                        <td>
                            <div class="font-bold">{{ $req->livro->nome }}</div>
                        </td>
                        <td>{{ $req->data_requisicao->format('d/m/Y') }}</td>
                        <td>{{ $req->data_prevista_devolucao->format('d/m/Y') }}</td>
                        <td>
                            @if($req->data_devolucao_real)
                                {{ $req->data_devolucao_real->format('d/m/Y') }}
                                @if($req->dias_atraso > 0)
                                    <span class="badge badge-error">+{{ $req->dias_atraso }} dias</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'pendente' => 'badge-warning',
                                    'ativo' => 'badge-info',
                                    'concluido' => 'badge-success',
                                    'atrasado' => 'badge-error',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$req->status] ?? 'badge-ghost' }}">
                                    {{ ucfirst($req->status) }}
                                </span>
                        </td>
                        <td>
                            <a href="{{ route('requisicoes.show', $req) }}" class="btn btn-xs btn-info">
                                Ver
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8">
                            Nenhuma requisição encontrada para este cidadão
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
