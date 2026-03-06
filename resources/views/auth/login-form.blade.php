<x-layouts.layout title="Login">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Login</h2>

                @if(session('sucesso'))
                    <div class="alert alert-success mb-4">{{ session('sucesso') }}</div>
                @endif

                @if(session('mensagem'))
                    <div class="alert alert-info mb-4">{{ session('mensagem') }}</div>
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

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Tipo de utilizador (hidden) --}}
                    @if(isset($tipo))
                        <input type="hidden" name="tipo" value="{{ $tipo }}">
                    @endif

                    {{-- Email --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Email</span>
                        </label>
                        <input type="email" name="email" placeholder="seu@email.com"
                               class="input input-bordered @error('email') input-error @enderror"
                               value="{{ old('email') }}" required autofocus>
                        @error('email')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Password</span>
                        </label>
                        <input type="password" name="password" placeholder="********"
                               class="input input-bordered @error('password') input-error @enderror" required>
                        @error('password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>


                    {{-- Botão de login --}}
                    <div class="form-control">
                        <button type="submit" class="btn btn-info text-white">
                            Entrar
                        </button>
                    </div>
                </form>

                {{-- Link para registo --}}
                <div class="text-center mt-4">
                    <p class="text-base-content/60">
                        Ainda não tem conta?
                        <a href="{{ route('register') }}" class="link link-info">Registe-se aqui</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
