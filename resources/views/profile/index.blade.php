<x-layouts.layout title="Meu Perfil">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Meu Perfil</h1>
            <a href="{{ route('home') }}" class="btn btn-ghost">
                Voltar
            </a>
        </div>

        @if(session('sucesso'))
            <div class="alert alert-success mb-6">{{ session('sucesso') }}</div>
        @endif

        @if(session('erro'))
            <div class="alert alert-error mb-6">{{ session('erro') }}</div>
        @endif

        {{-- FOTO DE PERFIL --}}
        <div class="card bg-base-100 shadow-xl mb-8">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Foto de Perfil</h2>

                <div class="flex flex-col items-center gap-4">
                    {{-- Avatar --}}
                    <div class="avatar">
                        <div class="w-32 h-32 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                            @if(auth()->user()->profile_photo_url)
                                <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />
                            @else
                                <div class="bg-neutral text-neutral-content text-4xl flex items-center justify-center w-full h-full">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2">
                        {{-- Upload de nova foto --}}
                        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*" onchange="this.form.submit()">
                            <button type="button" onclick="document.getElementById('profile_photo').click()" class="btn btn-info">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Alterar Foto
                            </button>
                        </form>

                        {{-- Remover foto --}}
                        @if(auth()->user()->profile_photo)
                            <form action="{{ route('profile.photo.delete') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-error">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remover
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Informações do Utilizador --}}
        <div class="card bg-base-100 shadow-xl mb-8">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Informações da Conta</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <span class="font-bold w-24">Nome:</span>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-bold w-24">Email:</span>
                        <span>{{ auth()->user()->email }}</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-bold w-24">Tipo:</span>
                        <span class="badge {{ auth()->user()->isBibliotecario() ? 'badge-warning' : 'badge-info' }}">
                            {{ auth()->user()->isBibliotecario() ? 'Bibliotecário' : 'Cliente' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALTERAR PASSWORD --}}
        <div class="card bg-base-100 shadow-xl mb-8">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Alterar Password</h2>

                <form method="POST" action="{{ route('profile.password') }}">
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
                        Atualizar Password
                    </button>
                </form>
            </div>
        </div>

        {{-- 2FA --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-4">Autenticação de Dois Fatores (2FA)</h2>

                @if(auth()->user()->two_factor_secret)
                    {{-- 2FA ATIVO --}}
                    <div class="alert alert-success mb-4">
                        <span>2FA está ativo na tua conta.</span>
                    </div>

                    @php
                        // Gerar QR Code SVG
                        $qrCode = auth()->user()->twoFactorQrCodeSvg();
                        $recoveryCodes = auth()->user()->recoveryCodes();
                    @endphp

                    @if($qrCode)
                        <div class="bg-base-200 p-6 rounded-box text-center mb-4">
                            <p class="font-medium mb-4">Escaneia este QR Code com o Google Authenticator</p>
                            <div class="flex justify-center bg-white p-4 rounded-box">
                                {!! $qrCode !!}
                            </div>
                        </div>
                    @endif

                    @if($recoveryCodes)
                        <div class="bg-base-200 p-6 rounded-box mb-4">
                            <p class="font-medium mb-2">Códigos de Recuperação</p>
                            <p class="text-sm text-base-content/70 mb-4">
                                Guarda estes códigos num local seguro. Eles permitem aceder à tua conta caso percas o acesso à app de autenticação.
                            </p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($recoveryCodes as $code)
                                    <div class="bg-base-100 p-2 rounded font-mono text-xs text-center">
                                        {{ $code }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <form action="{{ route('two-factor.disable') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error">
                                Desativar 2FA
                            </button>
                        </form>
                    </div>
                @else
                    {{-- 2FA INATIVO --}}
                    <div class="alert alert-warning mb-4">
                        <span>2FA não está ativo. Ativa para maior segurança.</span>
                    </div>

                    <form action="{{ route('two-factor.enable') }}" method="POST" class="flex justify-end">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Ativar 2FA
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</x-layouts.layout>
