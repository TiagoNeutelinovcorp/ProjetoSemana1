<x-layouts.layout title="Chat">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Chat</h1>

            @if(auth()->user()->isBibliotecario())
                <button onclick="document.getElementById('modalCriarSala').showModal()"
                        class="btn btn-info">
                    Criar Nova Sala
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Minhas Salas --}}
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-header bg-base-200 p-4 rounded-t-xl">
                        <h2 class="text-xl font-semibold">Minhas Salas</h2>
                    </div>
                    <div class="card-body p-0">
                        @forelse($salas as $sala)
                            <a href="{{ route('chat.show', $sala) }}"
                               class="flex items-center justify-between p-4 border-b hover:bg-base-200 transition">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">#</span>
                                        <span class="font-semibold">{{ $sala->nome }}</span>
                                        @if($sala->isAdmin(auth()->user()))
                                            <span class="badge badge-info badge-sm">Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-base-content/50 mt-1">
                                        Participantes: {{ $sala->participantes->count() }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-base-content/50">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Nenhuma sala de chat ainda.
                                <br>
                                <small>Os administradores podem criar salas.</small>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Todas as Salas (apenas admin) --}}
            @if(auth()->user()->isBibliotecario())
                <div class="lg:col-span-1">
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-header bg-base-200 p-4 rounded-t-xl">
                            <h2 class="text-xl font-semibold">Todas as Salas</h2>
                        </div>
                        <div class="card-body p-0">
                            @forelse($todasSalas as $sala)
                                <a href="{{ route('chat.show', $sala) }}"
                                   class="flex items-center justify-between p-4 border-b hover:bg-base-200 transition">
                                    <div>
                                        <span class="font-semibold">{{ $sala->nome }}</span>
                                        <div class="text-xs text-base-content/50">
                                            Criado por: {{ $sala->criador->name ?? 'Sistema' }}
                                        </div>
                                        <div class="text-xs text-base-content/50">
                                            Participantes: {{ $sala->participantes->count() }}
                                        </div>
                                    </div>
                                    <span class="text-sm">→</span>
                                </a>
                            @empty
                                <div class="p-8 text-center text-base-content/50">
                                    Nenhuma sala criada ainda.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal para criar sala --}}
    @if(auth()->user()->isBibliotecario())
        <dialog id="modalCriarSala" class="modal">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-lg mb-4">Criar Nova Sala de Chat</h3>
                <form method="POST" action="{{ route('chat.store') }}">
                    @csrf

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Nome da Sala *</span>
                        </label>
                        <input type="text" name="nome" class="input input-bordered" placeholder="Ex: Equipa de Projeto" required>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Descrição</span>
                        </label>
                        <textarea name="descricao" class="textarea textarea-bordered" rows="2" placeholder="Descreve o propósito da sala..."></textarea>
                    </div>

                    <div class="form-control mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="label">
                                <span class="label-text font-semibold">Adicionar Participantes</span>
                            </label>
                            <div class="flex gap-2">
                                <button type="button" id="selectAll" class="btn btn-xs btn-ghost">Selecionar Todos</button>
                                <button type="button" id="selectNone" class="btn btn-xs btn-ghost">Remover Todos</button>
                            </div>
                        </div>

                        <div class="border rounded-box p-3 max-h-64 overflow-y-auto bg-base-200">
                            @php
                                $outrosUtilizadores = \App\Models\User::where('id', '!=', auth()->id())->get();
                            @endphp

                            @forelse($outrosUtilizadores as $utilizador)
                                <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-base-300 cursor-pointer transition">
                                    <input type="checkbox" name="participantes[]" value="{{ $utilizador->id }}"
                                           class="checkbox checkbox-info checkbox-sm">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 flex items-center justify-center">
                                            <span class="text-xs uppercase">{{ substr($utilizador->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $utilizador->name }}</div>
                                        <div class="text-xs text-base-content/50">{{ $utilizador->email }}</div>
                                    </div>
                                    <div class="badge badge-ghost badge-sm">Membro</div>
                                </label>
                            @empty
                                <div class="text-center text-base-content/50 py-4">
                                    Não há outros utilizadores no sistema.
                                </div>
                            @endforelse
                        </div>
                        <label class="label">
                            <span class="label-text-alt text-info">Dica: Podes selecionar múltiplos utilizadores</span>
                        </label>
                    </div>

                    <div class="modal-action">
                        <button type="submit" class="btn btn-info">Criar Sala</button>
                        <button type="button" class="btn" onclick="document.getElementById('modalCriarSala').close()">Cancelar</button>
                    </div>
                </form>
            </div>
        </dialog>
    @endif

    @push('scripts')
    <script>
        document.getElementById('selectAll')?.addEventListener('click', () => {
            document.querySelectorAll('input[name="participantes[]"]').forEach(cb => cb.checked = true);
        });

        document.getElementById('selectNone')?.addEventListener('click', () => {
            document.querySelectorAll('input[name="participantes[]"]').forEach(cb => cb.checked = false);
        });
    </script>
    @endpush
</x-layouts.layout>
