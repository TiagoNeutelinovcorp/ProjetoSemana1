<x-layouts.layout title="Novo Autor">
    <div class="max-w-2xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Adicionar Novo Autor</h2>
                <form action="{{ route('autores.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-control">
                        <label class="label">Nome do Autor</label>
                        <input type="text" name="nome" class="input input-bordered @error('nome') input-error @enderror" value="{{ old('nome') }}" required>
                        @error('nome') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control mt-4">
                        <label class="label">Foto</label>
                        <input type="file" name="foto" class="file-input file-input-bordered file-input-info w-full @error('foto') file-input-error @enderror" accept="image/*">
                        @error('foto') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="card-actions justify-end mt-6">
                        <a href="{{ route('autores.index') }}" class="btn btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-info">Guardar Autor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.layout>
