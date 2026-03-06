<x-layouts.layout title="Gestão de Utilizadores">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Gestão de Utilizadores</h1>
            <a href="{{ route('profile.index') }}" class="btn btn-ghost">
                Voltar ao Perfil
            </a>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        @if(session('erro'))
            <div class="alert alert-error mb-6">{{ session('erro') }}</div>
        @endif

        {{-- Filtros --}}
        <div class="bg-base-200 p-4 rounded-box mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="form-control flex-1">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="pesquisa" value="{{ request('pesquisa') }}"
                           placeholder="Nome ou email"
                           class="input input-bordered w-full">
                </div>

                <div class="form-control w-40">
                    <label class="label">
                        <span class="label-text">Tipo</span>
                    </label>
                    <select name="role" class="select select-bordered">
                        <option value="">Todos</option>
                        <option value="cliente" {{ request('role') == 'cliente' ? 'selected' : '' }}>Clientes</option>
                        <option value="bibliotecario" {{ request('role') == 'bibliotecario' ? 'selected' : '' }}>Bibliotecários</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-info">Filtrar</button>
                    @if(request()->anyFilled(['pesquisa', 'role']))
                        <a href="{{ route('users.index') }}" class="btn btn-ghost">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabela de Utilizadores --}}
        <div class="bg-base-100 rounded-box shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead class="bg-base-200">
                    <tr>
                        <th class="text-xs uppercase tracking-wider">ID</th>
                        <th class="text-xs uppercase tracking-wider">Utilizador</th>
                        <th class="text-xs uppercase tracking-wider">Email</th>
                        <th class="text-xs uppercase tracking-wider">Tipo</th>
                        <th class="text-xs uppercase tracking-wider">2FA</th>
                        <th class="text-xs uppercase tracking-wider text-center">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr class="hover">
                            <td class="font-mono text-sm">{{ $user->id }}</td>
                            <td>
                                <div class="font-medium">{{ $user->name }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <form action="{{ route('users.update-role', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" onchange="this.form.submit()" class="select select-sm select-bordered w-32"
                                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <option value="cliente" {{ $user->role === 'cliente' ? 'selected' : '' }}>Cliente</option>
                                        <option value="bibliotecario" {{ $user->role === 'bibliotecario' ? 'selected' : '' }}>Bibliotecário</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                @if($user->two_factor_secret)
                                    <span class="badge badge-success badge-sm">Ativo</span>
                                @else
                                    <span class="badge badge-ghost badge-sm">Inativo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    {{-- BOTÃO DE HISTÓRICO --}}
                                    <a href="{{ route('requisicoes.historico', $user) }}"
                                       class="btn btn-xs btn-info">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Histórico
                                    </a>

                                    @if($user->id !== auth()->id())
                                        {{-- Botão que abre o modal de delete --}}
                                        <label for="delete-user-modal-{{ $user->id }}" class="btn btn-xs btn-error">
                                            Apagar
                                        </label>

                                        {{-- Modal de confirmação DaisyUI --}}
                                        <input type="checkbox" id="delete-user-modal-{{ $user->id }}" class="modal-toggle" />
                                        <div class="modal" role="dialog">
                                            <div class="modal-box">
                                                <h3 class="text-lg font-bold text-error">Confirmar Eliminação</h3>

                                                <div class="py-4">
                                                    <p>Tens a certeza que desejas apagar o utilizador
                                                        <span class="font-bold">"{{ $user->name }}"</span>?</p>
                                                    <p class="text-sm text-base-content/70 mt-2">
                                                        Email: {{ $user->email }}
                                                    </p>
                                                    <p class="text-sm text-base-content/70">
                                                        ID: {{ $user->id }}
                                                    </p>
                                                    <p class="text-sm text-error mt-4">
                                                        Esta ação não pode ser desfeita. Todas as requisições deste utilizador serão também apagadas.
                                                    </p>
                                                </div>

                                                <div class="modal-action">
                                                    <label for="delete-user-modal-{{ $user->id }}" class="btn btn-ghost">Cancelar</label>
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-error">
                                                            Sim, apagar utilizador
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-base-content/50">(tu)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="h-16 w-16 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <p class="text-base-content/60 text-lg">Nenhum utilizador encontrado</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Rodapé da tabela com contagem e paginação --}}
            <div class="bg-base-200 px-4 py-3 flex flex-col items-center gap-3 text-sm">

                {{-- Paginação --}}
                @if($users->hasPages())
                    <div class="join">
                        <a href="{{ $users->previousPageUrl() }}"
                           class="join-item btn btn-sm {{ $users->onFirstPage() ? 'btn-disabled' : '' }}">
                            «
                        </a>

                        @php
                            $start = max(1, $users->currentPage() - 2);
                            $end = min($users->lastPage(), $users->currentPage() + 2);
                        @endphp

                        @for($page = $start; $page <= $end; $page++)
                            <a href="{{ $users->url($page) }}"
                               class="join-item btn btn-sm {{ $page == $users->currentPage() ? 'btn-active' : '' }}">
                                {{ $page }}
                            </a>
                        @endfor

                        <a href="{{ $users->nextPageUrl() }}"
                           class="join-item btn btn-sm {{ !$users->hasMorePages() ? 'btn-disabled' : '' }}">
                            »
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.layout>
