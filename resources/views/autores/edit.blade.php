<x-layouts.layout title="Editar Autor">
    <div class="max-w-2xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Editar Autor</h2>

                <form action="{{ route('autores.update', $autor) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Nome do Autor --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-bold">Nome do Autor</span>
                        </label>
                        <input type="text" name="nome" class="input input-bordered @error('nome') input-error @enderror"
                               value="{{ old('nome', $autor->nome) }}" required>
                        @error('nome')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Foto atual --}}
                    @if($autor->foto)
                        <div class="mb-4">
                            <label class="label">
                                <span class="label-text font-bold">Foto Atual</span>
                            </label>
                            <img src="{{ $autor->foto_url }}" alt="{{ $autor->nome }}" class="w-32 h-32 object-cover rounded-lg">
                        </div>
                    @endif

                    {{-- Nova Foto --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-bold">Nova Foto (opcional)</span>
                            <span class="label-text-alt">Max 2MB (JPG, PNG, GIF)</span>
                        </label>

                        {{-- Preview da nova imagem --}}
                        <div class="mb-4" id="preview-container" style="display: none;">
                            <img id="preview" src="#" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-base-300">
                        </div>

                        <input type="file" name="foto" id="foto"
                               class="file-input file-input-bordered file-input-info w-full @error('foto') file-input-error @enderror"
                               accept="image/*" onchange="previewImage(this)">
                        @error('foto')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botões --}}
                    <div class="card-actions justify-end gap-4">
                        <a href="{{ route('autores.index') }}" class="btn btn-ghost">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-info">
                            Atualizar Autor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('preview-container');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
</x-layouts.layout>
