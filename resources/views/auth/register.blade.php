<x-layouts.layout title="Registar">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Registar</h2>

                @if(session('sucesso'))
                    <div class="alert alert-success mb-4">
                        {{ session('sucesso') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Nome --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Nome</span>
                        </label>
                        <input type="text" name="name" placeholder="Seu nome completo"
                               class="input input-bordered @error('name') input-error @enderror"
                               value="{{ old('name') }}" required autofocus>
                        @error('name')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Email</span>
                        </label>
                        <input type="email" name="email" placeholder="seu@email.com"
                               class="input input-bordered @error('email') input-error @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Tipo de Utilizador --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Tipo de Utilizador</span>
                        </label>
                        <select name="role" id="role" class="select select-bordered @error('role') select-error @enderror" required onchange="toggleSecretCode()">
                            <option value="" disabled selected>Selecione o tipo</option>
                            <option value="cliente" {{ old('role') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                            <option value="bibliotecario" {{ old('role') == 'bibliotecario' ? 'selected' : '' }}>Bibliotecário</option>
                        </select>
                        @error('role')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Campo de código secreto --}}
                    <div id="secret-code-field" class="form-control mb-4 hidden">
                        <label class="label">
                            <span class="label-text font-bold">Código Secreto</span>
                        </label>
                        <input type="password" name="secret_code" class="input input-bordered" placeholder="Código para bibliotecário"/>
                        @error('secret_code')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-base-content/50 mt-1">Apenas para contas de bibliotecário</p>
                    </div>

                    {{-- Palavra-passe --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Palavra-passe</span>
                        </label>
                        <input type="password" name="password" placeholder="********"
                               class="input input-bordered @error('password') input-error @enderror" required>
                        @error('password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirmar Palavra-passe --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Confirmar Palavra-passe</span>
                        </label>
                        <input type="password" name="password_confirmation" placeholder="********"
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
            </div>
        </div>
    </div>

    <script>
        function toggleSecretCode() {
            const role = document.getElementById('role').value;
            const secretField = document.getElementById('secret-code-field');

            if (role === 'bibliotecario') {
                secretField.classList.remove('hidden');
            } else {
                secretField.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleSecretCode();
        });
    </script>
</x-layouts.layout>
