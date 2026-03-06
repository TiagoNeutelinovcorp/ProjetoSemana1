<x-layouts.layout title="Recuperar Password">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Recuperar Password</h2>

                <p class="mb-4 text-center text-base-content/70">
                    Esqueceu-se da sua password? Introduza o seu email e enviaremos um link para redefinir.
                </p>

                @if(session('status'))
                    <div class="alert alert-success mb-4">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Email</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="input input-bordered" required autofocus>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-info text-white">
                            Enviar Link
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="link link-info">Voltar ao Login</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
