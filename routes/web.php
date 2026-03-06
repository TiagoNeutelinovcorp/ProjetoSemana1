<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\RequisicaoController;
use Maatwebsite\Excel\Facades\Excel;

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

// ==================== ROTAS 2FA (ACESSÍVEIS SEM 2FA) ====================
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

    // ==================== PERFIL DO UTILIZADOR (SEMPRE ACESSÍVEL) ====================
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [RouteController::class, 'profileIndex'])->name('index');
        Route::get('/edit', [RouteController::class, 'profileEdit'])->name('edit');
        Route::put('/update', [RouteController::class, 'profileUpdate'])->name('update');
        Route::put('/password', [RouteController::class, 'profilePassword'])->name('password');
    });

    // ==================== ROTAS QUE EXIGEM 2FA ====================
    Route::middleware(['two-factor'])->group(function () {

        // Minhas Requisições
        Route::get('/minhas-requisicoes', [RequisicaoController::class, 'minhasRequisicoes'])
            ->name('requisicoes.minhas');

        // Rotas para criar requisição
        Route::get('/requisicoes/create/{livro}', [RequisicaoController::class, 'create'])
            ->name('requisicoes.create');
        Route::post('/requisicoes', [RequisicaoController::class, 'store'])
            ->name('requisicoes.store');

        // Ver detalhes de uma requisição
        Route::get('/requisicoes/{requisicao}', [RequisicaoController::class, 'show'])
            ->name('requisicoes.show');

        // Cidadão solicita devolução
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
        });

        // EXPORTAÇÃO EXCEL (SÓ BIBLIOTECÁRIOS)
        Route::get('/exportar-livros', function() {
            try {
                return Excel::download(new \App\Exports\LivrosExport, 'livros_' . date('Y-m-d_H-i-s') . '.xlsx');
            } catch (\Exception $e) {
                return "Erro ao exportar: " . $e->getMessage();
            }
        })->name('livros.export')->middleware('can:isBibliotecario');

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
        Route::get('/requisicoes', [RequisicaoController::class, 'index'])
            ->name('requisicoes.index')
            ->middleware('can:isBibliotecario');

        // Admin confirma devolução
        Route::middleware(['can:isBibliotecario'])->group(function () {
            Route::put('/requisicoes/{requisicao}/confirmar-devolucao', [RequisicaoController::class, 'confirmarDevolucao'])
                ->name('requisicoes.confirmar-devolucao');
            Route::get('/requisicoes/historico/{user}', [RequisicaoController::class, 'historicoCidadao'])
                ->name('requisicoes.historico');
        });
    });
});


Route::get('/livros/{livro}/historico', [RequisicaoController::class, 'historicoLivro'])
    ->name('livros.historico')
    ->middleware(['auth', 'two-factor']);

Route::get('/requisicoes/historico/{user}', [RequisicaoController::class, 'historicoCidadao'])
    ->name('requisicoes.historico')
    ->middleware(['auth', 'two-factor', 'can:isBibliotecario']);

Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::post('/profile/photo', [RouteController::class, 'profileUpdatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [RouteController::class, 'profileDeletePhoto'])->name('profile.photo.delete');
});
