<x-layouts.layout title="Redefinir Password">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Redefinir Password</h2>

                @if($errors->any())
                    <div class="alert alert-error mb-4">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Email</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $email) }}"
                               class="input input-bordered" required autofocus>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Nova Password</span>
                        </label>
                        <input type="password" name="password"
                               class="input input-bordered" required>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Confirmar Password</span>
                        </label>
                        <input type="password" name="password_confirmation"
                               class="input input-bordered" required>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-info text-white">
                            Redefinir Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
