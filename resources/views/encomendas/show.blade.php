<x-layouts.layout title="Detalhe da Encomenda">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold">Encomenda #{{ $encomenda->numero_encomenda }}</h1>
                <p class="text-base-content/70 mt-1">
                    Realizada em {{ $encomenda->created_at->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->isBibliotecario() ? 'admin.encomendas.index' : 'encomendas.minhas') }}"
                   class="btn btn-ghost">
                    ← Voltar
                </a>
                @if(auth()->user()->isBibliotecario())
                    <label for="status-modal" class="btn btn-warning">
                        Atualizar Status
                    </label>
                @endif
            </div>
        </div>

        {{-- Status atual --}}
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <div class="flex items-center gap-4">
                    <div class="stat">
                        <div class="stat-title">Status Atual</div>
                        <div class="stat-value text-2xl">
                            <span class="badge badge-{{ $encomenda->status_cor }} badge-lg">
                                {{ $encomenda->status_texto }}
                            </span>
                        </div>
                        @if($encomenda->pago_em)
                            <div class="stat-desc">Pago em {{ $encomenda->pago_em->format('d/m/Y H:i') }}</div>
                        @endif
                        @if($encomenda->enviado_em)
                            <div class="stat-desc">Enviado em {{ $encomenda->enviado_em->format('d/m/Y') }}</div>
                        @endif
                        @if($encomenda->entregue_em)
                            <div class="stat-desc">Entregue em {{ $encomenda->entregue_em->format('d/m/Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Itens da encomenda --}}
            <div class="md:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Itens da Encomenda</h2>

                        <div class="space-y-4">
                            @foreach($encomenda->itens as $item)
                                <div class="flex gap-4 p-2 hover:bg-base-200 rounded-lg transition">
                                    <figure class="w-16 h-20 flex-shrink-0">
                                        <img src="{{ $item->livro->imagem_capa_url }}"
                                             alt="{{ $item->livro->nome }}"
                                             class="w-full h-full object-cover rounded">
                                    </figure>
                                    <div class="flex-1">
                                        <h3 class="font-bold">{{ $item->livro->nome }}</h3>
                                        <p class="text-sm text-base-content/70">
                                            {{ $item->livro->autores->first()->nome ?? 'Autor desconhecido' }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-2 text-sm">
                                            <span>{{ $item->quantidade }} x € {{ number_format($item->preco_unitario, 2, ',', '.') }}</span>
                                            <span class="font-bold text-success">
                                                € {{ number_format($item->subtotal, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumo e morada --}}
            <div class="space-y-6">
                {{-- Resumo de valores --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Resumo</h2>

                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>€ {{ number_format($encomenda->subtotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>IVA (23%):</span>
                                <span>€ {{ number_format($encomenda->iva, 2, ',', '.') }}</span>
                            </div>
                            <div class="divider my-2"></div>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-success">€ {{ number_format($encomenda->total, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Morada de entrega --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Morada de Entrega</h2>

                        <div class="space-y-1 text-sm">
                            <p><span class="font-bold">Morada:</span> {{ $encomenda->morada }}</p>
                            <p><span class="font-bold">Cidade:</span> {{ $encomenda->cidade }}</p>
                            <p><span class="font-bold">C. Postal:</span> {{ $encomenda->codigo_postal }}</p>
                            <p><span class="font-bold">País:</span> {{ $encomenda->pais }}</p>
                            <p><span class="font-bold">Telefone:</span> {{ $encomenda->telefone }}</p>
                        </div>
                    </div>
                </div>

                {{-- Método de pagamento --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Pagamento</h2>

                        <div class="space-y-1 text-sm">
                            <p><span class="font-bold">Método:</span> Stripe (Cartão)</p>
                            <p><span class="font-bold">ID:</span> {{ substr($encomenda->stripe_payment_intent_id, -8) }}</p>
                            @if($encomenda->pago_em)
                                <p><span class="font-bold">Pago em:</span> {{ $encomenda->pago_em->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Histórico de estados --}}
        @if($encomenda->historico->isNotEmpty())
            <div class="mt-6">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Histórico da Encomenda</h2>

                        <div class="timeline timeline-vertical timeline-compact">
                            @foreach($encomenda->historico->sortByDesc('created_at') as $historico)
                                <div class="timeline-item">
                                    <div class="timeline-start timeline-box bg-base-200 text-sm">
                                        {{ $historico->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="timeline-middle">
                                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="timeline-end timeline-box">
                                        <span class="badge badge-sm mb-1">de {{ $historico->estado_anterior ?? '-' }} para {{ $historico->estado_novo }}</span>
                                        @if($historico->observacoes)
                                            <p class="text-xs text-base-content/70 mt-1">{{ $historico->observacoes }}</p>
                                        @endif
                                        @if($historico->user)
                                            <p class="text-xs text-base-content/50 mt-1">por {{ $historico->user->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(auth()->user()->isBibliotecario())
        {{-- Modal para atualizar status --}}
        <input type="checkbox" id="status-modal" class="modal-toggle" />
        <div class="modal" role="dialog">
            <div class="modal-box">
                <h3 class="text-lg font-bold">Atualizar Status da Encomenda</h3>

                <form action="{{ route('admin.encomendas.status', $encomenda) }}" method="POST" class="mt-4">
                    @csrf
                    @method('PUT')

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Novo Status</span>
                        </label>
                        <select name="status" class="select select-bordered" required>
                            <option value="pendente_pagamento" {{ $encomenda->status == 'pendente_pagamento' ? 'selected' : '' }}>Aguardar Pagamento</option>
                            <option value="pago" {{ $encomenda->status == 'pago' ? 'selected' : '' }}>Pago</option>
                            <option value="processando" {{ $encomenda->status == 'processando' ? 'selected' : '' }}>Processando</option>
                            <option value="enviado" {{ $encomenda->status == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            <option value="entregue" {{ $encomenda->status == 'entregue' ? 'selected' : '' }}>Entregue</option>
                            <option value="cancelado" {{ $encomenda->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Observações (opcional)</span>
                        </label>
                        <textarea name="observacoes" class="textarea textarea-bordered" rows="3"></textarea>
                    </div>

                    <div class="modal-action">
                        <label for="status-modal" class="btn btn-ghost">Cancelar</label>
                        <button type="submit" class="btn btn-warning">Atualizar Status</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</x-layouts.layout>
