<x-layouts.layout title="Registar">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Registar</h2>

                @if(session('sucesso'))
                    <div class="alert alert-success mb-4">{{ session('sucesso') }}</div>
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

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Nome --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Nome</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="input input-bordered @error('name') input-error @enderror" required autofocus>
                        @error('name')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Email</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="input input-bordered @error('email') input-error @enderror" required>
                        @error('email')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Password</span>
                        </label>
                        <input type="password" name="password"
                               class="input input-bordered @error('password') input-error @enderror" required>
                        @error('password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirmar Password --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Confirmar Password</span>
                        </label>
                        <input type="password" name="password_confirmation"
                               class="input input-bordered" required>
                    </div>

                    {{-- Botão de registo --}}
                    <div class="form-control">
                        <button type="submit" class="btn btn-info text-white">
                            Registar
                        </button>
                    </div>
                </form>

                {{-- Link para login --}}
                <div class="text-center mt-4">
                    <p class="text-base-content/60">
                        Já tem conta?
                        <a href="{{ route('login') }}" class="link link-info">Login</a>
                    </p>
                </div>

                {{-- Informação sobre primeiro utilizador (opcional) --}}
                @if(\App\Models\User::count() === 0)
                    <div class="alert alert-info mt-4 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>O primeiro utilizador a registar-se será o administrador da biblioteca.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.layout>
