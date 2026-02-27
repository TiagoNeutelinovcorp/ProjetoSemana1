<x-layouts.layout title="Escolha o tipo de acesso">
    <div class="min-h-screen bg-base-200 py-12">
        <div class="container mx-auto px-4">
            {{-- CABEÇALHO --}}
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold mb-4">Aceder à Biblioteca</h1>
                <p class="text-xl text-base-content/70">Escolha o seu tipo de acesso</p>
            </div>

            {{-- CARDS LADO A LADO --}}
            <div class="flex flex-wrap justify-center gap-8 max-w-4xl mx-auto">

                {{-- CARD CLIENTE --}}
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 w-80">
                    <figure class="px-10 pt-10">
                        <div class="w-24 h-24 rounded-full bg-info/20 flex items-center justify-center">
                            <svg class="w-12 h-12 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h2 class="card-title text-3xl text-info">Cliente</h2>
                        <p class="text-base-content/70">Acesso básico à biblioteca</p>

                        <div class="text-left mt-4 space-y-2 w-full">
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Consultar livros</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Pesquisar autores</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Ver editoras</span>
                            </p>
                        </div>

                        <div class="card-actions w-full mt-6">
                            <a href="{{ route('login.cliente') }}" class="btn btn-info btn-block text-white">
                                Entrar como Cliente
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- CARD BIBLIOTECÁRIO --}}
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 w-80">
                    <figure class="px-10 pt-10">
                        <div class="w-24 h-24 rounded-full bg-warning/20 flex items-center justify-center">
                            <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </figure>
                    <div class="card-body items-center text-center">
                        <h2 class="card-title text-3xl text-warning">Bibliotecário</h2>
                        <p class="text-base-content/70">Gestão da biblioteca</p>

                        <div class="text-left mt-4 space-y-2 w-full">
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Adicionar livros</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Gerir a biblioteca</span>
                            </p>
                        </div>

                        <div class="card-actions w-full mt-6">
                            <a href="{{ route('login.bibliotecario') }}" class="btn btn-warning btn-block text-white">
                                Entrar como Bibliotecário
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LINK PARA REGISTO --}}
            <div class="text-center mt-12">
                <p class="text-base-content/60">
                    Ainda não tem conta?
                    <a href="{{ route('register') }}" class="link link-info font-bold text-lg">Registe-se aqui</a>
                </p>
            </div>
        </div>
    </div>
</x-layouts.layout>
