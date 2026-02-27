<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouteController;

// ==================== ROTAS PÚBLICAS ====================
Route::get('/', [RouteController::class, 'home'])->name('home');

// ==================== ROTA DE EXPORTAÇÃO (IGUAL À QUE FUNCIONOU) ====================
Route::get('/livros/exportar', function() {
    try {
        return Excel::download(new \App\Exports\LivrosExport, 'livros_' . date('Y-m-d_H-i-s') . '.xlsx');
    } catch (\Exception $e) {
        return "Erro: " . $e->getMessage();
    }
})->name('livros.export');

// ==================== AUTENTICAÇÃO ====================
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [RouteController::class, 'loginCards'])->name('login');
    Route::get('/login/cliente', [RouteController::class, 'loginCliente'])->name('login.cliente');
    Route::post('/login/cliente', [RouteController::class, 'loginStore']);
    Route::get('/login/bibliotecario', [RouteController::class, 'loginBibliotecario'])->name('login.bibliotecario');
    Route::post('/login/bibliotecario', [RouteController::class, 'loginStore']);
    Route::get('/register', [RouteController::class, 'registerForm'])->name('register');
    Route::post('/register', [RouteController::class, 'registerStore']);
});

Route::post('/logout', [RouteController::class, 'logout'])->middleware('auth')->name('logout');

// ==================== ROTAS PROTEGIDAS ====================
Route::middleware(['auth'])->group(function () {

    // LIVROS
    Route::get('/livros', [RouteController::class, 'livrosIndex'])->name('livros.index');
    Route::get('/livros/create', [RouteController::class, 'livrosCreate'])->name('livros.create');
    Route::post('/livros', [RouteController::class, 'livrosStore'])->name('livros.store');
    Route::get('/livros/{id}', [RouteController::class, 'livrosShow'])->name('livros.show');
    Route::get('/livros/{id}/edit', [RouteController::class, 'livrosEdit'])->name('livros.edit');
    Route::put('/livros/{id}', [RouteController::class, 'livrosUpdate'])->name('livros.update');
    Route::delete('/livros/{id}', [RouteController::class, 'livrosDestroy'])->name('livros.destroy');

    // AUTORES
    Route::get('/autores', [RouteController::class, 'autoresIndex'])->name('autores.index');
    Route::get('/autores/create', [RouteController::class, 'autoresCreate'])->name('autores.create');
    Route::post('/autores', [RouteController::class, 'autoresStore'])->name('autores.store');
    Route::get('/autores/{id}', [RouteController::class, 'autoresShow'])->name('autores.show');
    Route::get('/autores/{id}/edit', [RouteController::class, 'autoresEdit'])->name('autores.edit');
    Route::put('/autores/{id}', [RouteController::class, 'autoresUpdate'])->name('autores.update');
    Route::delete('/autores/{id}', [RouteController::class, 'autoresDestroy'])->name('autores.destroy');

    // EDITORAS
    Route::get('/editoras', [RouteController::class, 'editorasIndex'])->name('editoras.index');
    Route::get('/editoras/create', [RouteController::class, 'editorasCreate'])->name('editoras.create');
    Route::post('/editoras', [RouteController::class, 'editorasStore'])->name('editoras.store');
    Route::get('/editoras/{id}', [RouteController::class, 'editorasShow'])->name('editoras.show');
    Route::get('/editoras/{id}/edit', [RouteController::class, 'editorasEdit'])->name('editoras.edit');
    Route::put('/editoras/{id}', [RouteController::class, 'editorasUpdate'])->name('editoras.update');
    Route::delete('/editoras/{id}', [RouteController::class, 'editorasDestroy'])->name('editoras.destroy');
});
