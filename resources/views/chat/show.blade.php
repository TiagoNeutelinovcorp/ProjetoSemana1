<x-layouts.layout title="Chat - {{ $chatRoom->nome }}">
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('chat.index') }}" class="btn btn-ghost btn-sm">
                Voltar
            </a>
            <h1 class="text-2xl font-bold">{{ $chatRoom->nome }}</h1>
            @if($chatRoom->isAdmin(auth()->user()))
                <span class="badge badge-info">Admin</span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Area das mensagens --}}
            <div class="lg:col-span-3">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body p-4">
                        <div id="messages-container" class="h-[500px] overflow-y-auto space-y-3 mb-4 p-2">
                            @foreach($messages as $message)
                                <div class="chat {{ $message->user_id == auth()->id() ? 'chat-end' : 'chat-start' }}">
                                    <div class="chat-image avatar">
                                        <div class="w-8 rounded-full">
                                            @if($message->user->profile_photo)
                                                <img src="{{ asset('storage/' . $message->user->profile_photo) }}"
                                                     alt="{{ $message->user->name }}"
                                                     class="rounded-full w-8 h-8 object-cover">
                                            @else
                                                <div class="bg-info text-info-content rounded-full w-8 h-8 flex items-center justify-center font-bold">
                                                    {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chat-header">
                                        {{ $message->user->name }}
                                        <time class="text-xs opacity-50 ml-1">{{ $message->created_at->format('H:i') }}</time>
                                    </div>
                                    <div class="chat-bubble {{ $message->user_id == auth()->id() ? 'chat-bubble-info' : 'chat-bubble-secondary' }}">
                                        {{ $message->message }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{ $messages->links() }}

                        <form id="send-message-form" class="flex gap-2 mt-2">
                            @csrf
                            <input type="text" id="message-input"
                                   class="input input-bordered flex-1"
                                   placeholder="Escreve a tua mensagem..."
                                   autocomplete="off">
                            <button type="submit" class="btn btn-info">
                                Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Participantes --}}
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-header bg-base-200 p-4 rounded-t-xl">
                        <h2 class="text-lg font-semibold">Participantes ({{ $participantes->count() }})</h2>
                    </div>
                    <div class="card-body p-0">
                        @foreach($participantes as $participante)
                            <div class="flex items-center justify-between p-3 border-b">
                                <div class="flex items-center gap-2">
                                    <div class="avatar">
                                        <div class="w-8 rounded-full">
                                            @if($participante->profile_photo)
                                                <img src="{{ asset('storage/' . $participante->profile_photo) }}"
                                                     alt="{{ $participante->name }}"
                                                     class="rounded-full w-8 h-8 object-cover">
                                            @else
                                                <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">
                                                    {{ strtoupper(substr($participante->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $participante->name }}</div>
                                        <div class="text-xs text-base-content/50">{{ $participante->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if($participante->pivot->role == 'admin')
                                        <span class="badge badge-info badge-sm">Admin</span>
                                    @endif

                                    @if($chatRoom->isAdmin(auth()->user()) && $participante->id != auth()->id() && $participante->pivot->role != 'admin')
                                        <button onclick="confirmKick({{ $participante->id }}, '{{ $participante->name }}')"
                                                class="btn btn-xs btn-ghost text-error hover:bg-error/20">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($chatRoom->isAdmin(auth()->user()))
                        <div class="card-footer p-4 border-t">
                            <button onclick="document.getElementById('modalAddUser').showModal()"
                                    class="btn btn-sm btn-outline w-full">
                                Adicionar Utilizador
                            </button>
                        </div>
                    @endif
                </div>

                <form action="{{ route('chat.leave', $chatRoom) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-error w-full"
                            onclick="return confirm('Tens a certeza que queres sair desta sala?')">
                        Sair da Sala
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para adicionar utilizador --}}
    @if($chatRoom->isAdmin(auth()->user()))
        <dialog id="modalAddUser" class="modal">
            <div class="modal-box max-w-md">
                <h3 class="font-bold text-lg mb-4">Adicionar Utilizador</h3>

                @php
                    $disponiveis = \App\Models\User::whereNotIn('id', $participantes->pluck('id'))
                        ->where('id', '!=', auth()->id())
                        ->get();
                @endphp

                @if($disponiveis->count() > 0)
                    <form method="POST" action="{{ route('chat.add-participant', $chatRoom) }}" id="form-add-user">
                        @csrf
                        <div class="form-control mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="label">
                                    <span class="label-text font-semibold">Selecionar Utilizadores</span>
                                </label>
                                <div class="flex gap-2">
                                    <button type="button" id="selectAllUsers" class="btn btn-xs btn-ghost">Selecionar Todos</button>
                                    <button type="button" id="selectNoneUsers" class="btn btn-xs btn-ghost">Remover Todos</button>
                                </div>
                            </div>

                            <div class="border rounded-box p-3 max-h-64 overflow-y-auto bg-base-200">
                                @foreach($disponiveis as $utilizador)
                                    <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-base-300 cursor-pointer transition">
                                        <input type="checkbox" name="user_ids[]" value="{{ $utilizador->id }}"
                                               class="checkbox checkbox-info checkbox-sm user-checkbox">
                                        <div class="avatar">
                                            <div class="w-8 rounded-full">
                                                @if($utilizador->profile_photo)
                                                    <img src="{{ asset('storage/' . $utilizador->profile_photo) }}"
                                                         alt="{{ $utilizador->name }}"
                                                         class="rounded-full w-8 h-8 object-cover">
                                                @else
                                                    <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 flex items-center justify-center text-xs font-bold">
                                                        {{ strtoupper(substr($utilizador->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium">{{ $utilizador->name }}</div>
                                            <div class="text-xs text-base-content/50">{{ $utilizador->email }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <span id="selectedCount" class="text-xs text-info">0 selecionados</span>
                        </div>

                        <div class="modal-action">
                            <button type="submit" class="btn btn-info">Adicionar Selecionados</button>
                            <button type="button" class="btn" onclick="document.getElementById('modalAddUser').close()">Cancelar</button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <span>Não há utilizadores disponíveis para adicionar.</span>
                    </div>
                    <div class="modal-action">
                        <button type="button" class="btn" onclick="document.getElementById('modalAddUser').close()">Fechar</button>
                    </div>
                @endif
            </div>
        </dialog>
    @endif

    <script>
        const chatRoomId = {{ $chatRoom->id }};
        let kickUserId = null;
        let kickUserName = '';

        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }

        document.getElementById('send-message-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();

            const input = document.getElementById('message-input');
            const message = input.value.trim();

            if (!message) return;

            try {
                const response = await fetch('{{ route("chat.send-message", $chatRoom) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message })
                });

                if (response.ok) {
                    input.value = '';
                    location.reload();
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        });

        setInterval(() => {
            location.reload();
        }, 5000);

        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('#form-add-user .user-checkbox');
            const checked = document.querySelectorAll('#form-add-user .user-checkbox:checked');
            const countSpan = document.getElementById('selectedCount');
            if (countSpan) {
                countSpan.textContent = checked.length + ' selecionado' + (checked.length !== 1 ? 's' : '');
            }
        }

        document.getElementById('selectAllUsers')?.addEventListener('click', () => {
            document.querySelectorAll('#form-add-user .user-checkbox').forEach(cb => cb.checked = true);
            updateSelectedCount();
        });

        document.getElementById('selectNoneUsers')?.addEventListener('click', () => {
            document.querySelectorAll('#form-add-user .user-checkbox').forEach(cb => cb.checked = false);
            updateSelectedCount();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('#form-add-user .user-checkbox');
            checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));
            updateSelectedCount();
        });

        function confirmKick(userId, userName) {
            kickUserId = userId;
            kickUserName = userName;
            document.getElementById('kickUserName').innerHTML = `Tens a certeza que queres remover <strong>${userName}</strong> desta sala?`;
            document.getElementById('modalConfirmKick').showModal();
        }

        document.getElementById('confirmKickBtn')?.addEventListener('click', function() {
            if (kickUserId) {
                fetch(`/chat/${chatRoomId}/kick/${kickUserId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        document.getElementById('modalConfirmKick').close();
                        location.reload();
                    } else {
                        document.getElementById('modalConfirmKick').close();
                        alert('Erro ao remover utilizador.');
                    }
                }).catch(() => {
                    document.getElementById('modalConfirmKick').close();
                    alert('Erro ao remover utilizador.');
                });
            }
        });
    </script>

    {{-- Modal de confirmação para remover utilizador --}}
    <dialog id="modalConfirmKick" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold text-error">Confirmar Remoção</h3>
            <div class="py-4">
                <p id="kickUserName" class="mb-2">Tens a certeza que queres remover <strong></strong> desta sala?</p>
                <p class="text-sm text-base-content/70">Esta ação é irreversível. O utilizador será removido da sala e perderá acesso às mensagens.</p>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn btn-ghost">Cancelar</button>
                </form>
                <button id="confirmKickBtn" class="btn btn-error">Remover</button>
            </div>
        </div>
    </dialog>
</x-layouts.layout>
