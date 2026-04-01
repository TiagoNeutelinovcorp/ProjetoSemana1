<x-layouts.layout title="Logs do Sistema">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Logs do Sistema</h1>
        </div>

        {{-- Filtros --}}
        <div class="bg-base-200 rounded-box shadow-md p-4 mb-6">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="form-control w-40">
                    <label class="label">
                        <span class="label-text text-xs">Módulo</span>
                    </label>
                    <select name="modulo" class="select select-bordered select-sm">
                        <option value="">Todos</option>
                        <option value="requisicoes" {{ request('modulo') == 'requisicoes' ? 'selected' : '' }}>Requisições</option>
                        <option value="livros" {{ request('modulo') == 'livros' ? 'selected' : '' }}>Livros</option>
                        <option value="autores" {{ request('modulo') == 'autores' ? 'selected' : '' }}>Autores</option>
                        <option value="editoras" {{ request('modulo') == 'editoras' ? 'selected' : '' }}>Editoras</option>
                        <option value="utilizadores" {{ request('modulo') == 'utilizadores' ? 'selected' : '' }}>Utilizadores</option>
                        <option value="encomendas" {{ request('modulo') == 'encomendas' ? 'selected' : '' }}>Encomendas</option>
                    </select>
                </div>

                <div class="form-control w-40">
                    <label class="label">
                        <span class="label-text text-xs">Ação</span>
                    </label>
                    <select name="acao" class="select select-bordered select-sm">
                        <option value="">Todos</option>
                        <option value="criar" {{ request('acao') == 'criar' ? 'selected' : '' }}>Criar</option>
                        <option value="editar" {{ request('acao') == 'editar' ? 'selected' : '' }}>Editar</option>
                        <option value="apagar" {{ request('acao') == 'apagar' ? 'selected' : '' }}>Apagar</option>
                        <option value="devolver" {{ request('acao') == 'devolver' ? 'selected' : '' }}>Devolver</option>
                    </select>
                </div>

                <div class="form-control flex-1">
                    <label class="label">
                        <span class="label-text text-xs">Utilizador</span>
                    </label>
                    <input type="text" name="user" value="{{ request('user') }}"
                           placeholder="Nome do utilizador..."
                           class="input input-bordered input-sm w-full">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm">Filtrar</button>
                    <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
                </div>
            </form>
        </div>

        {{-- Tabela de logs --}}
        <div class="bg-base-100 rounded-box shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead class="bg-base-200">
                        <tr>
                            <th>Data/Hora</th>
                            <th>Utilizador</th>
                            <th>Módulo</th>
                            <th>Ação</th>
                            <th class="min-w-[250px]">Alteração</th>
                            <th>IP</th>
                            <th>Browser</th>
                        </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="hover">
                                <td class="whitespace-nowrap text-sm">
                                    {{ $log->created_at->format('d/m/Y') }}
                                    <div class="text-xs text-base-content/50">{{ $log->created_at->format('H:i:s') }}</div>
                                </td>
                                <td>
                                    @if($log->user)
                                        {{ $log->user->name }}
                                        <div class="text-xs text-base-content/50">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-base-content/50">Sistema</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-sm">
                                        {{ ucfirst($log->modulo) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $acoesCores = [
                                            'criar' => 'badge-success',
                                            'editar' => 'badge-warning',
                                            'apagar' => 'badge-error',
                                            'devolver' => 'badge-info',
                                            'teste_manual' => 'badge-secondary',
                                        ];
                                    @endphp
                                    <span class="badge {{ $acoesCores[$log->acao] ?? 'badge-ghost' }} badge-sm">
                                        {{ ucfirst(str_replace('_', ' ', $log->acao)) }}
                                    </span>
                                    @if($log->objeto_id)
                                        <div class="text-xs text-base-content/50 mt-1">ID: {{ $log->objeto_id }}</div>
                                    @endif
                                </td>
                                <td class="align-top py-2">
                                    @php
                                        $alteracao = is_string($log->alteracao) ? json_decode($log->alteracao, true) : $log->alteracao;
                                    @endphp

                                    @if($alteracao && is_array($alteracao) && count($alteracao) > 0)
                                        <div class="space-y-1">
                                            @foreach($alteracao as $campo => $valor)
                                                <div class="text-xs border-b border-base-200 pb-1 last:border-0 last:pb-0">
                                                    <span class="font-semibold text-primary">
                                                        {{ ucfirst(str_replace('_', ' ', $campo)) }}:
                                                    </span>
                                                    <span class="text-base-content/70 break-words">
                                                        {{ is_array($valor) ? implode(', ', $valor) : $valor }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-base-content/30 italic text-sm">—</span>
                                    @endif
                                </td>
                                <td class="text-xs font-mono">{{ $log->ip }}</td>
                                <td class="text-xs break-words max-w-[200px]">{{ $log->browser }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <div class="text-base-content/50">
                                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Nenhum log encontrado
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="bg-base-200 px-4 py-3 flex justify-center">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.layout>
