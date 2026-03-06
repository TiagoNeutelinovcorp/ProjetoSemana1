<x-layouts.layout title="Confirmar Password">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6 text-center mx-auto">Confirmar Password</h2>

                <p class="mb-4 text-center text-base-content/70">
                    Esta é uma área segura. Por favor confirme a sua password antes de continuar.
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

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Password</span>
                        </label>
                        <input type="password" name="password"
                               class="input input-bordered" required autofocus>
                    </div>

                    <div class="form-control">
                        <button type="submit" class="btn btn-info text-white">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
