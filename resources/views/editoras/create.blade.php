<x-layouts.layout title="Nova Editora">
    <div class="max-w-2xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Adicionar Nova Editora</h2>

                <form action="{{ route('editoras.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Nome da Editora --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Nome da Editora</span>
                        </label>
                        <input type="text" name="nome" placeholder="Ex: Porto Editora"
                               class="input input-bordered @error('nome') input-error @enderror"
                               value="{{ old('nome') }}" required>
                        @error('nome')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Logotipo --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-medium">Logotipo</span>
                            <span class="label-text-alt text-base-content/70">Max 2MB (JPG, PNG, GIF)</span>
                        </label>

                        {{-- Preview da imagem --}}
                        <div class="mb-4" id="preview-container" style="display: none;">
                            <img id="preview" src="#" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-base-300">
                        </div>

                        <input type="file" name="logotipo" id="logotipo"
                               class="file-input file-input-bordered file-input-info w-full @error('logotipo') file-input-error @enderror"
                               accept="image/*" onchange="previewImage(this)">
                        @error('logotipo')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botões --}}
                    <div class="card-actions justify-end gap-4">
                        <a href="{{ route('editoras.index') }}" class="btn btn-ghost">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-info">
                            Guardar Editora
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
