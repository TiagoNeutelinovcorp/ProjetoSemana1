<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RequisicaoController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\GoogleBooksController;

// ==================== ROTAS PÚBLICAS ====================
Route::get('/', [RouteController::class, 'home'])->name('home');

// ==================== ROTAS PÚBLICAS DE LISTAGEM ====================
Route::get('/livros', [RouteController::class, 'livrosIndex'])->name('livros.index');
Route::get('/livros/{id}', [RouteController::class, 'livrosShow'])->name('livros.show');
Route::get('/autores', [RouteController::class, 'autoresIndex'])->name('autores.index');
Route::get('/autores/{id}', [RouteController::class, 'autoresShow'])->name('autores.show');
Route::get('/editoras', [RouteController::class, 'editorasIndex'])->name('editoras.index');
Route::get('/editoras/{id}', [RouteController::class, 'editorasShow'])->name('editoras.show');

// ==================== AUTENTICAÇÃO ====================
Route::get('/login', [RouteController::class, 'loginCards'])->name('login');
Route::get('/login/cliente', [RouteController::class, 'loginCliente'])->name('login.cliente');
Route::get('/login/bibliotecario', [RouteController::class, 'loginBibliotecario'])->name('login.bibliotecario');
Route::post('/login', [RouteController::class, 'loginStore'])->name('login');
Route::get('/register', [RouteController::class, 'registerForm'])->name('register');
Route::post('/register', [RouteController::class, 'registerStore'])->name('register');
Route::post('/logout', [RouteController::class, 'logout'])->name('logout');

// ==================== ROTAS 2FA ====================
Route::middleware(['auth'])->group(function () {
    Route::post('/two-factor/enable', [RouteController::class, 'twoFactorEnable'])->name('two-factor.enable');
    Route::delete('/two-factor/disable', [RouteController::class, 'twoFactorDisable'])->name('two-factor.disable');
});

Route::get('/two-factor-challenge', function() {
    return view('auth.two-factor-challenge');
})->name('two-factor.challenge')->middleware('guest');

Route::post('/two-factor-challenge', [RouteController::class, 'twoFactorVerify'])->name('two-factor.verify');

// ==================== ROTAS PROTEGIDAS (AUTH) ====================
Route::middleware(['auth'])->group(function () {

    // ==================== PERFIL DO UTILIZADOR ====================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [RouteController::class, 'profileIndex'])->name('index');
        Route::get('/edit', [RouteController::class, 'profileEdit'])->name('edit');
        Route::put('/update', [RouteController::class, 'profileUpdate'])->name('update');
        Route::put('/password', [RouteController::class, 'profilePassword'])->name('password');
        Route::post('/photo', [RouteController::class, 'profileUpdatePhoto'])->name('photo.update');
        Route::delete('/photo', [RouteController::class, 'profileDeletePhoto'])->name('photo.delete');
    });

    // ==================== ROTAS QUE EXIGEM 2FA ====================
    Route::middleware(['two-factor'])->group(function () {

        // ==================== MINHAS REQUISIÇÕES ====================
        Route::get('/minhas-requisicoes', [RequisicaoController::class, 'minhasRequisicoes'])
            ->name('requisicoes.minhas');

        // ==================== CRIAÇÃO DE REQUISIÇÕES ====================
        Route::get('/requisicoes/create/{livro}', [RequisicaoController::class, 'create'])
            ->name('requisicoes.create');
        Route::post('/requisicoes', [RequisicaoController::class, 'store'])
            ->name('requisicoes.store');
        Route::get('/requisicoes/{requisicao}', [RequisicaoController::class, 'show'])
            ->name('requisicoes.show');
        Route::post('/requisicoes/{requisicao}/solicitar-devolucao', [RequisicaoController::class, 'solicitarDevolucao'])
            ->name('requisicoes.solicitar-devolucao');

        // ==================== GESTÃO DE UTILIZADORES (SÓ BIBLIOTECÁRIOS) ====================
        Route::prefix('users')->name('users.')->middleware('can:isBibliotecario')->group(function () {
            Route::get('/', [RouteController::class, 'usersIndex'])->name('index');
            Route::put('/{user}/role', [RouteController::class, 'usersUpdateRole'])->name('update-role');
            Route::delete('/{user}', [RouteController::class, 'usersDestroy'])->name('destroy');
        });

        // ==================== CRUD DE LIVROS (SÓ BIBLIOTECÁRIOS) ====================
        Route::middleware(['can:isBibliotecario'])->group(function () {
            Route::get('/livros/create', [RouteController::class, 'livrosCreate'])->name('livros.create');
            Route::post('/livros', [RouteController::class, 'livrosStore'])->name('livros.store');
            Route::get('/livros/{id}/edit', [RouteController::class, 'livrosEdit'])->name('livros.edit');
            Route::put('/livros/{id}', [RouteController::class, 'livrosUpdate'])->name('livros.update');
            Route::delete('/livros/{id}', [RouteController::class, 'livrosDestroy'])->name('livros.destroy');
            Route::get('/criar-livro', [RouteController::class, 'livrosCreate'])->name('criar.livro');
            Route::get('/exportar-livros', function() {
                try {
                    return Excel::download(new \App\Exports\LivrosExport, 'livros_' . date('Y-m-d_H-i-s') . '.xlsx');
                } catch (\Exception $e) {
                    return "Erro ao exportar: " . $e->getMessage();
                }
            })->name('livros.export');
        });

        // ==================== CRUD DE AUTORES (SÓ BIBLIOTECÁRIOS) ====================
        Route::middleware(['can:isBibliotecario'])->group(function () {
            Route::get('/autores/create', [RouteController::class, 'autoresCreate'])->name('autores.create');
            Route::post('/autores', [RouteController::class, 'autoresStore'])->name('autores.store');
            Route::get('/autores/{id}/edit', [RouteController::class, 'autoresEdit'])->name('autores.edit');
            Route::put('/autores/{id}', [RouteController::class, 'autoresUpdate'])->name('autores.update');
            Route::delete('/autores/{id}', [RouteController::class, 'autoresDestroy'])->name('autores.destroy');
        });

        // ==================== CRUD DE EDITORAS (SÓ BIBLIOTECÁRIOS) ====================
        Route::middleware(['can:isBibliotecario'])->group(function () {
            Route::get('/editoras/create', [RouteController::class, 'editorasCreate'])->name('editoras.create');
            Route::post('/editoras', [RouteController::class, 'editorasStore'])->name('editoras.store');
            Route::get('/editoras/{id}/edit', [RouteController::class, 'editorasEdit'])->name('editoras.edit');
            Route::put('/editoras/{id}', [RouteController::class, 'editorasUpdate'])->name('editoras.update');
            Route::delete('/editoras/{id}', [RouteController::class, 'editorasDestroy'])->name('editoras.destroy');
        });

        // ==================== REQUISIÇÕES (ADMIN) ====================
        Route::middleware(['can:isBibliotecario'])->group(function () {
            Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
            Route::put('/requisicoes/{requisicao}/confirmar-devolucao', [RequisicaoController::class, 'confirmarDevolucao'])
                ->name('requisicoes.confirmar-devolucao');
        });
    });
});

// ==================== ROTAS DE HISTÓRICO ====================
Route::get('/livros/{livro}/historico', [RequisicaoController::class, 'historicoLivro'])
    ->name('livros.historico')
    ->middleware(['auth', 'two-factor', 'can:isBibliotecario']);

Route::get('/requisicoes/historico/{user}', [RequisicaoController::class, 'historicoCidadao'])
    ->name('requisicoes.historico')
    ->middleware(['auth', 'two-factor', 'can:isBibliotecario']);

// ==================== GOOGLE BOOKS (TODOS OS AUTENTICADOS) ====================
Route::middleware(['auth'])->group(function () {
    Route::get('/pesquisar-livros', [GoogleBooksController::class, 'showSearchForm'])->name('google-books.search.form');
    Route::get('/pesquisar-livros/resultados', [GoogleBooksController::class, 'search'])->name('google-books.search');
    Route::post('/pesquisar-livros/sugerir', [GoogleBooksController::class, 'sugerir'])->name('google-books.sugerir');
});

// ==================== GOOGLE BOOKS (SÓ ADMIN) ====================
Route::middleware(['auth', 'two-factor', 'can:isBibliotecario'])->group(function () {
    Route::post('/pesquisar-livros/importar', [GoogleBooksController::class, 'import'])->name('google-books.import');
    Route::get('/sugestoes-livros', [GoogleBooksController::class, 'listarSugestoes'])->name('google-books.sugestoes');
    Route::post('/sugestoes/{sugestao}/aprovar', [GoogleBooksController::class, 'aprovarSugestao'])->name('google-books.sugestoes.aprovar');
    Route::post('/sugestoes/{sugestao}/rejeitar', [GoogleBooksController::class, 'rejeitarSugestao'])->name('google-books.sugestoes.rejeitar');
});
