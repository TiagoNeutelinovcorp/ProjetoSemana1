<x-layouts.layout title="Editar Perfil">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('profile.index') }}" class="btn btn-ghost btn-circle">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold">Editar Perfil</h1>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        {{-- Formulário de Informações Pessoais --}}
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Informações Pessoais</h2>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Nome</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="input input-bordered @error('name') input-error @enderror" required>
                        @error('name')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Email</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="input input-bordered @error('email') input-error @enderror" required>
                        @error('email')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-info">
                        Atualizar Informações
                    </button>
                </form>
            </div>
        </div>

        {{-- Formulário de Alterar Password --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Alterar Password</h2>

                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Password Atual</span>
                        </label>
                        <input type="password" name="current_password"
                               class="input input-bordered @error('current_password') input-error @enderror" required>
                        @error('current_password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Nova Password</span>
                        </label>
                        <input type="password" name="new_password"
                               class="input input-bordered @error('new_password') input-error @enderror" required>
                        @error('new_password')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Confirmar Nova Password</span>
                        </label>
                        <input type="password" name="new_password_confirmation"
                               class="input input-bordered" required>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        Alterar Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
