<x-layouts.layout title="Verificar Email">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <h2 class="card-title text-2xl mb-6 justify-center">Verificar Email</h2>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success mb-6">
                        Um novo link de verificação foi enviado para o teu email!
                    </div>
                @else
                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>
                            Enviámos um link de verificação para o teu email.<br>
                            Por favor, verifica a tua caixa de entrada.
                        </span>
                    </div>
                @endif

                <p class="mb-4">Não recebeste o email?</p>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-info w-full">
                        Reenviar email de verificação
                    </button>
                </form>

                <div class="mt-6">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="link link-info">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
