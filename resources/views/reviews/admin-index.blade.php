<x-layouts.layout title="Gestão de Reviews">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold">Gestão de Reviews</h1>
                <p class="text-base-content/70 mt-1">Aprovar ou recusar reviews de livros</p>
            </div>
        </div>

        {{-- Cards de estatísticas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-warning">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Pendentes</div>
                <div class="stat-value text-3xl text-warning font-bold">{{ $estatisticas['pendentes'] }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-success">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Ativos</div>
                <div class="stat-value text-3xl text-success font-bold">{{ $estatisticas['ativos'] }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow-lg p-6">
                <div class="stat-figure text-error">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
                <div class="stat-title text-base-content/70">Recusados</div>
                <div class="stat-value text-3xl text-error font-bold">{{ $estatisticas['recusados'] }}</div>
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
                        <option value="suspenso" {{ request('status') == 'suspenso' ? 'selected' : '' }}>Pendentes</option>
                        <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativos</option>
                        <option value="recusado" {{ request('status') == 'recusado' ? 'selected' : '' }}>Recusados</option>
                    </select>
                </div>

                <div class="form-control w-52">
                    <label class="label">
                        <span class="label-text text-xs">Livro</span>
                    </label>
                    <input type="text" name="livro" value="{{ request('livro') }}"
                           placeholder="Título do livro..."
                           class="input input-bordered input-sm w-full">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-info btn-sm">Filtrar</button>
                    @if(request()->anyFilled(['status', 'livro']))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost btn-sm">Limpar</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabela de reviews --}}
        <div class="bg-base-100 rounded-box shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead class="bg-base-200">
                    <tr>
                        <th>ID</th>
                        <th>Livro</th>
                        <th>Cidadão</th>
                        <th>Classificação</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($reviews as $review)
                        <tr class="hover">
                            <td class="font-mono text-sm">#{{ $review->id }}</td>
                            <td>
                                <div class="font-medium">{{ $review->livro->nome }}</div>
                            </td>
                            <td>
                                <div>{{ $review->user->name }}</div>
                                <div class="text-xs text-base-content/50">{{ $review->user->email }}</div>
                            </td>
                            <td>
                                <div class="rating rating-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <span class="mask mask-star-2 bg-orange-400 w-4 h-4"></span>
                                        @else
                                            <span class="mask mask-star-2 bg-gray-300 w-4 h-4"></span>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td>{{ $review->created_at->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $statusClasses = [
                                        'suspenso' => 'badge-warning',
                                        'ativo' => 'badge-success',
                                        'recusado' => 'badge-error',
                                    ];
                                    $statusTexts = [
                                        'suspenso' => 'Pendente',
                                        'ativo' => 'Ativo',
                                        'recusado' => 'Recusado',
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$review->status] }} badge-sm">
                                        {{ $statusTexts[$review->status] }}
                                    </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-xs btn-info">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8">
                                Nenhuma review encontrada
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($reviews->hasPages())
                <div class="bg-base-200 px-4 py-3 flex justify-center">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.layout>
