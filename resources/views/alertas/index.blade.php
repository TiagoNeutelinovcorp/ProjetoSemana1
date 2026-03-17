<x-layouts.layout title="Meus Alertas">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Meus Alertas</h1>
            <a href="{{ route('profile.index') }}" class="btn btn-ghost">
                Voltar ao Perfil
            </a>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        @if($alertas->isEmpty())
            <div class="text-center py-16 bg-base-200 rounded-box">
                <svg class="h-24 w-24 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <p class="text-base-content/60 text-xl mb-4">Não tens alertas ativos</p>
                <a href="{{ route('livros.index') }}" class="btn btn-info">Ver Livros</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($alertas as $alerta)
                    <div class="card bg-base-100 shadow-xl">
                        <div class="card-body">
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-20 flex-shrink-0">
                                    <img src="{{ $alerta->livro->imagem_capa_url }}"
                                         alt="{{ $alerta->livro->nome }}"
                                         class="w-full h-full object-cover rounded">
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold line-clamp-2">{{ $alerta->livro->nome }}</h3>
                                    <p class="text-sm text-base-content/70">
                                        {{ $alerta->livro->autores->first()->nome ?? 'Autor desconhecido' }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="badge badge-warning badge-sm">Pendente</span>
                                        <span class="text-xs text-base-content/50">
                                            {{ $alerta->created_at->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    <div class="card-actions justify-end mt-4">
                                        <form action="{{ route('alertas.cancelar', $alerta) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-error"
                                                    onclick="return confirm('Cancelar este alerta?')">
                                                Cancelar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($alertas->hasPages())
                <div class="mt-6 flex justify-center">
                    {{ $alertas->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.layout>
