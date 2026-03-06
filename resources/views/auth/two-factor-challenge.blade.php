<x-layouts.layout title="Verificação 2FA">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Autenticação de Dois Fatores</h2>

                <p class="mb-4 text-center text-base-content/70">
                    Introduza o código gerado pela sua aplicação de autenticação.
                </p>

                @if($errors->any())
                    <div class="alert alert-error mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('two-factor.verify') }}">
                    @csrf

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Código 2FA</span>
                        </label>
                        <input type="text" name="code" placeholder="000000"
                               class="input input-bordered text-center text-2xl tracking-widest"
                               maxlength="6" pattern="[0-9]*" inputmode="numeric" required autofocus>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-info text-white">
                            Verificar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
