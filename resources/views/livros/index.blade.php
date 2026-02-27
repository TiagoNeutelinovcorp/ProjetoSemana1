<x-layouts.layout title="Início">
    {{-- TÍTULO E INTRODUÇÃO --}}
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold mb-4">Biblioteca</h1>
        <p class="text-xl text-base-content/70 max-w-2xl mx-auto">
            Bem-vindo à nossa biblioteca. Crie a sua conta gratuita para aceder a todo o catálogo de livros, autores e editoras.
        </p>

        {{-- MENSAGEM PARA AUTENTICADOS --}}
        @auth
            <div class="mt-6">
                <p class="text-lg">Bem-vindo de volta, <span class="font-bold">{{ auth()->user()->name }}</span>!</p>
            </div>
        @endauth
    </div>

    {{-- CARDS DE NAVEGAÇÃO --}}
    <div class="flex flex-wrap justify-center gap-8 max-w-7xl mx-auto">

        {{-- CARD LIVROS --}}
        <div class="card w-96 bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="card-body">
                <span class="badge badge-xs badge-info">Coleção Principal</span>
                <div class="flex justify-between items-center">
                    <h2 class="text-3xl font-bold">Livros</h2>
                </div>

                <ul class="mt-6 flex flex-col gap-2 text-sm">
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Consultar catálogo completo</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Pesquisar por título ou ISBN</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Ver detalhes de cada livro</span>
                    </li>
                </ul>

                <div class="mt-6">
                    <a href="{{ route('livros.index') }}" class="btn btn-info btn-block">
                        Ver Livros
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- CARD AUTORES --}}
        <div class="card w-96 bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="card-body">
                <span class="badge badge-xs badge-warning">Escritores</span>
                <div class="flex justify-between items-center">
                    <h2 class="text-3xl font-bold">Autores</h2>
                </div>

                <ul class="mt-6 flex flex-col gap-2 text-sm">
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Lista completa de autores</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Ver livros de cada autor</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Fotos e informações</span>
                    </li>
                </ul>

                <div class="mt-6">
                    <a href="{{ route('autores.index') }}" class="btn btn-warning btn-block">
                        Ver Autores
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- CARD EDITORAS --}}
        <div class="card w-96 bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
            <div class="card-body">
                <span class="badge badge-xs badge-success">Publicadoras</span>
                <div class="flex justify-between items-center">
                    <h2 class="text-3xl font-bold">Editoras</h2>
                </div>

                <ul class="mt-6 flex flex-col gap-2 text-sm">
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Catálogo de editoras</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Livros publicados por cada</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Logótipos e informações</span>
                    </li>
                </ul>

                <div class="mt-6">
                    <a href="{{ route('editoras.index') }}" class="btn btn-success btn-block">
                        Ver Editoras
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.layout>
