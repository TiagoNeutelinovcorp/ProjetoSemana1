<x-layouts.layout title="Pesquisar na Google Books">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Pesquisar Livros na Google</h1>

        <form action="{{ route('google-books.search') }}" method="GET" class="bg-base-200 p-6 rounded-box shadow-md">
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-bold">O que procuras?</span>
                </label>
                <input type="text" name="query" placeholder="Título, autor, ISBN..."
                       class="input input-bordered w-full" value="{{ request('query') }}" required>
            </div>
            <div class="form-control mt-4">
                <button type="submit" class="btn btn-info">Pesquisar</button>
            </div>
        </form>
    </div>
</x-layouts.layout>
