<x-layouts.layout title="Gestão de Encomendas">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Gestão de Encomendas</h1>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        @if(session('erro'))
            <div class="alert alert-error mb-6">{{ session('erro') }}</div>
        @endif

        {{-- Cards de estatísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-warning">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Aguardar Pagamento</div>
                <div class="stat-value text-3xl text-warning font-bold">{{ $estatisticas['pendentes'] ?? 0 }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-success">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Pagas Hoje</div>
                <div class="stat-value text-3xl text-success font-bold">{{ $estatisticas['pagas_hoje'] ?? 0 }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-info">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Total do Mês</div>
                <div class="stat-value text-3xl text-info font-bold">€ {{ number_format($estatisticas['total_mes'] ?? 0, 2, ',', '.') }}</div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-base-200 rounded-box shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="form-control w-40">
                    <label class="label">
                        <span class="label-text text-xs">Status</span>
                    </label>
                    <select name="status" class="select select-bordered select-sm">
                        <option value="">Todos</option>
                        <option value="pendente_pagamento" {{ request('status') == 'pendente_pagamento' ? 'selected' : '' }}>Aguardar Pagamento</option>
                        <option value="pago" {{ request('status') == 'pago' ? 'selected' : '' }}>Pago</option>
                        <option value="processando" {{ request('status') == 'processando' ? 'selected' : '' }}>Processando</option>
                        <option value="enviado" {{ request('status') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                        <option value="entregue" {{ request('status') == 'entregue' ? 'selected' : '' }}>Entregue</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="form-control flex-1">
                    <label class="label">
                        <span class="label-text text-xs">Pesquisar</span>
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nº Encomenda ou Nome do Cliente..."
                           class="input input-bordered input-sm w-full">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm">Filtrar</button>
                    @if(request()->anyFilled(['status', 'search']))
                        <a href="{{ route('admin.encomendas.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabela de encomendas --}}
        <div class="bg-base-100 rounded-box shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead class="bg-base-200">
                        <tr>
                            <th>Nº Encomenda</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </thead>
                    <tbody>
                        @forelse($encomendas as $encomenda)
                            <tr class="hover">
                                <td class="font-mono text-sm">{{ $encomenda->numero_encomenda }}</td>
                                <td>
                                    <div class="font-medium">{{ $encomenda->user->name }}</div>
                                    <div class="text-xs text-base-content/50">{{ $encomenda->user->email }}</div>
                                </td>
                                <td>{{ $encomenda->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-bold text-success">€ {{ number_format($encomenda->total, 2, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusCores = [
                                            'pendente_pagamento' => 'badge-warning',
                                            'pago' => 'badge-info',
                                            'processando' => 'badge-info',
                                            'enviado' => 'badge-primary',
                                            'entregue' => 'badge-success',
                                            'cancelado' => 'badge-error',
                                        ];
                                        $statusTextos = [
                                            'pendente_pagamento' => 'Aguardar Pagamento',
                                            'pago' => 'Pago',
                                            'processando' => 'Processando',
                                            'enviado' => 'Enviado',
                                            'entregue' => 'Entregue',
                                            'cancelado' => 'Cancelado',
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusCores[$encomenda->status] ?? 'badge-ghost' }} badge-sm">
                                        {{ $statusTextos[$encomenda->status] ?? $encomenda->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('encomendas.show', $encomenda) }}" class="btn btn-xs btn-info">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    Nenhuma encomenda encontrada
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($encomendas->hasPages())
                <div class="bg-base-200 px-4 py-3 flex justify-center">
                    {{ $encomendas->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.layout>
