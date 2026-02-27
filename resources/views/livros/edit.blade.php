<x-layouts.layout title="Editar Livro">
    <div class="max-w-3xl mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-2xl mb-6">Editar Livro</h2>

                <form action="{{ route('livros.update', $livro) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Linha 1: ISBN e Nome --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- ISBN --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">ISBN</span>
                            </label>
                            <input type="text" name="isbn" placeholder="978-3-16-148410-0"
                                   class="input input-bordered @error('isbn') input-error @enderror"
                                   value="{{ old('isbn', $livro->isbn) }}" required>
                            @error('isbn')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Nome --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Nome do Livro</span>
                            </label>
                            <input type="text" name="nome" placeholder="Título do livro"
                                   class="input input-bordered @error('nome') input-error @enderror"
                                   value="{{ old('nome', $livro->nome) }}" required>
                            @error('nome')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Linha 2: Editora e Preço --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- Editora --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Editora</span>
                            </label>
                            <select name="editora_id" class="select select-bordered @error('editora_id') select-error @enderror" required>
                                <option value="" disabled>Selecione uma editora</option>
                                @foreach($editoras as $editora)
                                    <option value="{{ $editora->id }}"
                                        {{ old('editora_id', $livro->editora_id) == $editora->id ? 'selected' : '' }}>
                                        {{ $editora->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('editora_id')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Preço --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium">Preço (€)</span>
                            </label>
                            <input type="number" step="0.01" min="0" name="preco" placeholder="29.90"
                                   class="input input-bordered @error('preco') input-error @enderror"
                                   value="{{ old('preco', $livro->preco) }}" required>
                            @error('preco')
                            <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Autor (dropdown único) --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Autor</span>
                        </label>
                        <select name="autor_id" class="select select-bordered @error('autor_id') select-error @enderror" required>
                            <option value="" disabled>Selecione um autor</option>
                            @foreach($autores as $autor)
                                <option value="{{ $autor->id }}"
                                    {{ old('autor_id', $autorSelecionado) == $autor->id ? 'selected' : '' }}>
                                    {{ $autor->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('autor_id')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Bibliografia --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Bibliografia / Descrição</span>
                        </label>
                        <textarea name="bibliografia" rows="4"
                                  class="textarea textarea-bordered @error('bibliografia') textarea-error @enderror"
                                  placeholder="Descrição do livro, sinopse, etc...">{{ old('bibliografia', $livro->bibliografia) }}</textarea>
                        @error('bibliografia')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Imagem da Capa --}}
                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text font-medium">Imagem da Capa</span>
                        </label>

                        {{-- Imagem atual --}}
                        @if($livro->imagem_capa)
                            <div class="mb-4">
                                <p class="text-sm text-base-content/70 mb-2">Imagem atual:</p>
                                <img src="{{ $livro->imagem_capa_url }}" alt="Capa atual" class="w-32 h-32 object-cover rounded-lg border-2 border-base-300">
                            </div>
                        @endif

                        {{-- Preview da nova imagem --}}
                        <div class="mb-4" id="preview-container" style="display: none;">
                            <p class="text-sm text-base-content/70 mb-2">Nova imagem:</p>
                            <img id="preview" src="#" alt="Preview" class="w-32 h-32 object-cover rounded-lg border-2 border-base-300">
                        </div>

                        <input type="file" name="imagem_capa" id="imagem_capa"
                               class="file-input file-input-bordered file-input-info w-full @error('imagem_capa') file-input-error @enderror"
                               accept="image/*" onchange="previewImage(this)">
                        @error('imagem_capa')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Botões --}}
                    <div class="card-actions justify-end gap-4">
                        <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-info">
                            Atualizar Livro
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
