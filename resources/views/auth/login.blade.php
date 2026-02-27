<x-layouts.layout title="Login">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">
                    @if(request()->routeIs('login.cliente'))
                        Login Cliente
                    @elseif(request()->routeIs('login.bibliotecario'))
                        Login Bibliotecário
                    @else
                        Login
                    @endif
                </h2>

                {{-- Mensagens de erro --}}
                @if($errors->any())
                    <div class="alert alert-error mb-4">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Formulário de login --}}
                <form method="POST" action="{{ request()->routeIs('login.cliente') ? route('login.cliente') : route('login.bibliotecario') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Email</span>
                        </label>
                        <input type="email"
                               name="email"
                               placeholder="seu@email.com"
                               class="input input-bordered @error('email') input-error @enderror"
                               value="{{ old('email') }}"
                               required
                               autofocus>
                    </div>

                    {{-- Palavra-passe --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Palavra-passe</span>
                        </label>
                        <input type="password"
                               name="password"
                               placeholder="********"
                               class="input input-bordered @error('password') input-error @enderror"
                               required>
                    </div>



                    {{-- Botão de login --}}
                    <div class="form-control">
                        <button type="submit" class="btn btn-info text-white">
                            Entrar
                        </button>
                    </div>
                </form>

                {{-- Links --}}
                <div class="text-center mt-4 space-y-2">
                    <p class="text-base-content/60">
                        Ainda não tem conta?
                        <a href="{{ route('register') }}" class="link link-info">Registe-se aqui</a>
                    </p>

                    <p class="text-sm mt-4">
                        <a href="{{ route('login') }}" class="link link-info">
                            ← Voltar à escolha de tipo
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
