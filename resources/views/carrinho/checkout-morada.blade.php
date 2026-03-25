<x-layouts.layout title="Checkout - Morada">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('carrinho.index') }}" class="btn btn-ghost btn-circle">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold">Checkout - Passo 1 de 2</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Formulário de morada --}}
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-2xl mb-4">Morada de Entrega</h2>

                        <form action="{{ route('checkout.processar-morada') }}" method="POST">
                            @csrf

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-bold">Morada</span>
                                </label>
                                <input type="text" name="morada" class="input input-bordered w-full" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-bold">Cidade</span>
                                    </label>
                                    <input type="text" name="cidade" class="input input-bordered" required>
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-bold">Código Postal</span>
                                    </label>
                                    <input type="text" name="codigo_postal" class="input input-bordered" required>
                                </div>
                            </div>

                            <div class="form-control mb-4">
                                <label class="label">
                                    <span class="label-text font-bold">Telefone</span>
                                </label>
                                <input type="tel" name="telefone" class="input input-bordered" required>
                            </div>

                            <div class="form-control mb-6">
                                <label class="label">
                                    <span class="label-text font-bold">País</span>
                                </label>
                                <select name="pais" class="select select-bordered">
                                    <option value="Portugal">Portugal</option>
                                    <option value="Espanha">Espanha</option>
                                    <option value="França">França</option>
                                </select>
                            </div>

                            <div class="card-actions justify-end">
                                <button type="submit" class="btn btn-info">
                                    Continuar para Pagamento
                                    <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Resumo do carrinho --}}
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-lg sticky top-6">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">Resumo</h2>

                        <div class="space-y-4">
                            @foreach($itens as $item)
                                <div class="flex gap-2">
                                    <div class="w-12 h-16 flex-shrink-0">
                                        <img src="{{ $item->livro->imagem_capa_url }}"
                                             alt="{{ $item->livro->nome }}"
                                             class="w-full h-full object-cover rounded">
                                    </div>
                                    <div class="flex-1 text-sm">
                                        <p class="font-medium line-clamp-2">{{ $item->livro->nome }}</p>
                                        <p class="text-xs text-base-content/50">{{ $item->quantidade }} x € {{ number_format($item->livro->preco, 2, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach

                            <div class="divider my-2"></div>

                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span>Subtotal:</span>
                                    <span>€ {{ number_format($total, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>IVA (23%):</span>
                                    <span>€ {{ number_format($iva, 2, ',', '.') }}</span>
                                </div>
                                <div class="divider my-2"></div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-success">€ {{ number_format($totalComIva, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
