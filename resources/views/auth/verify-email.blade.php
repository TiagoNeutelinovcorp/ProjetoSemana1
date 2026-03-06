<x-layouts.layout title="Verificar Email">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Verifique o seu email</h2>

                @if(session('sucesso'))
                    <div class="alert alert-success mb-4">{{ session('sucesso') }}</div>
                @endif

                @if(session('mensagem'))
                    <div class="alert alert-info mb-4">{{ session('mensagem') }}</div>
                @endif

                <p class="mb-4">
                    Enviámos um link de verificação para <strong>{{ auth()->user()->email }}</strong>
                </p>

                <p class="mb-4 text-sm text-base-content/70">
                    Clique no link do email para ativar a sua conta.
                </p>

                <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                    @csrf
                    <button type="submit" class="btn btn-info w-full text-white">
                        Reenviar email de verificação
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost w-full">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
