<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;

class RouteController extends Controller
{
    // ==================== PÁGINAS PÚBLICAS ====================

    public function home()
    {
        return view('livros.index');
    }

    // ==================== AUTENTICAÇÃO ====================

    public function loginCards()
    {
        return view('auth.login-cards');
    }

    public function loginCliente()
    {
        return view('auth.login', ['tipo' => 'cliente']);
    }

    public function loginBibliotecario()
    {
        return view('auth.login', ['tipo' => 'bibliotecario']);
    }

    public function loginStore(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (is_null(Auth::user()->email_verified_at)) {
                return redirect()->route('verification.notice')
                    ->with('mensagem', 'Por favor verifique o seu email antes de aceder.');
            }

            return redirect()->intended('/livros');
        }

        return back()->withErrors([
            'email' => 'As credenciais não correspondem.',
        ])->onlyInput('email');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function registerStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:cliente,bibliotecario',
            'secret_code' => 'required_if:role,bibliotecario|nullable|string'
        ]);

        if ($request->role === 'bibliotecario') {
            $codigoCorreto = env('SECRET_CODE_BIBLIOTECARIO', 'biblioteca2025');
            if ($request->secret_code !== $codigoCorreto) {
                return back()->withErrors([
                    'secret_code' => 'Código secreto inválido para bibliotecário.'
                ])->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('sucesso', 'Registo realizado! Por favor verifique o seu email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // ==================== VERIFICAÇÃO DE EMAIL ====================

    public function verificationNotice()
    {
        return view('auth.verify-email');
    }

    public function verificationVerify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/livros')->with('sucesso', 'Email verificado com sucesso!');
    }

    public function verificationSend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('sucesso', 'Link de verificação reenviado!');
    }

    // ==================== LIVROS ====================

    public function livrosIndex(Request $request)
    {
        $query = Livro::with(['editora', 'autores']);

        // FILTRO POR PESQUISA (nome do livro ou ISBN)
        if ($request->filled('pesquisa')) {
            $pesquisa = $request->pesquisa;
            $query->where(function($q) use ($pesquisa) {
                $q->where('nome', 'like', "%{$pesquisa}%")
                    ->orWhere('isbn', 'like', "%{$pesquisa}%");
            });
        }

        // FILTRO POR AUTOR
        if ($request->filled('autor')) {
            $query->whereHas('autores', function($q) use ($request) {
                $q->where('autores.id', $request->autor);
            });
        }

        // FILTRO POR EDITORA
        if ($request->filled('editora')) {
            $query->where('editora_id', $request->editora);
        }

        // ORDENAÇÃO E PAGINAÇÃO
        $livros = $query->latest()->paginate(8)->withQueryString();

        // DADOS PARA OS SELECTS DOS FILTROS
        $autores = Autor::orderBy('nome')->get();
        $editoras = Editora::orderBy('nome')->get();

        return view('livros.livros', compact('livros', 'autores', 'editoras'));
    }
    public function livrosCreate()
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }
        $editoras = Editora::orderBy('nome')->get();
        $autores = Autor::orderBy('nome')->get();
        return view('livros.create', compact('editoras', 'autores'));
    }

    public function livrosStore(Request $request)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'isbn' => 'required|unique:livros,isbn',
            'nome' => 'required|string|max:255',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'editora_id' => 'required|exists:editoras,id',
            'autor_id' => 'required|exists:autores,id',
            'imagem_capa' => 'nullable|image|max:2048'
        ]);

        $imagemPath = null;
        if ($request->hasFile('imagem_capa')) {
            $imagemPath = $request->file('imagem_capa')->store('livros', 'public');
        }

        $livro = Livro::create([
            'isbn' => $request->isbn,
            'nome' => $request->nome,
            'bibliografia' => $request->bibliografia,
            'preco' => $request->preco,
            'editora_id' => $request->editora_id,
            'imagem_capa' => $imagemPath,
        ]);

        $livro->autores()->attach($request->autor_id);
        return redirect()->route('livros.index')->with('sucesso', 'Livro adicionado!');
    }

    public function livrosShow($id)
    {
        $livro = Livro::with(['editora', 'autores'])->findOrFail($id);
        return view('livros.show', compact('livro'));
    }

    public function livrosEdit($id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $livro = Livro::with('autores')->findOrFail($id);
        $editoras = Editora::orderBy('nome')->get();
        $autores = Autor::orderBy('nome')->get();
        $autorSelecionado = $livro->autores->first()->id ?? null;
        return view('livros.edit', compact('livro', 'editoras', 'autores', 'autorSelecionado'));
    }

    public function livrosUpdate(Request $request, $id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $livro = Livro::findOrFail($id);

        $request->validate([
            'isbn' => 'required|unique:livros,isbn,' . $id,
            'nome' => 'required|string|max:255',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'editora_id' => 'required|exists:editoras,id',
            'autor_id' => 'required|exists:autores,id',
            'imagem_capa' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('imagem_capa')) {
            if ($livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }
            $livro->imagem_capa = $request->file('imagem_capa')->store('livros', 'public');
        }

        $livro->update([
            'isbn' => $request->isbn,
            'nome' => $request->nome,
            'bibliografia' => $request->bibliografia,
            'preco' => $request->preco,
            'editora_id' => $request->editora_id,
        ]);

        $livro->autores()->sync($request->autor_id);
        return redirect()->route('livros.index')->with('sucesso', 'Livro atualizado!');
    }

    public function livrosDestroy($id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $livro = Livro::findOrFail($id);

        if ($livro->imagem_capa) {
            Storage::disk('public')->delete($livro->imagem_capa);
        }

        $livro->autores()->detach();
        $livro->delete();
        return redirect()->route('livros.index')->with('sucesso', 'Livro removido!');
    }

    // ==================== AUTORES ====================

    public function autoresIndex()
    {
        $autores = Autor::with('livros')->orderBy('nome')->paginate(8);
        return view('autores.autores', compact('autores'));
    }

    public function autoresCreate()
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }
        return view('autores.create');
    }

    public function autoresStore(Request $request)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048'
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('autores', 'public');
        }

        Autor::create([
            'nome' => $request->nome,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('autores.index')->with('sucesso', 'Autor adicionado!');
    }

    public function autoresShow($id)
    {
        $autor = Autor::with('livros')->findOrFail($id);
        return view('autores.show', compact('autor'));
    }

    public function autoresEdit($id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $autor = Autor::findOrFail($id);
        return view('autores.edit', compact('autor'));
    }

    public function autoresUpdate(Request $request, $id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $autor = Autor::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            if ($autor->foto) {
                Storage::disk('public')->delete($autor->foto);
            }
            $autor->foto = $request->file('foto')->store('autores', 'public');
        }

        $autor->update(['nome' => $request->nome]);
        return redirect()->route('autores.index')->with('sucesso', 'Autor atualizado!');
    }

    public function autoresDestroy($id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $autor = Autor::findOrFail($id);

        if ($autor->livros()->count() > 0) {
            return redirect()->route('autores.index')->with('erro', 'Autor tem livros associados!');
        }

        if ($autor->foto) {
            Storage::disk('public')->delete($autor->foto);
        }

        $autor->delete();
        return redirect()->route('autores.index')->with('sucesso', 'Autor removido!');
    }

    // ==================== EDITORAS ====================

    public function editorasIndex()
    {
        $editoras = Editora::with('livros')->orderBy('nome')->paginate(8);
        return view('editoras.editoras', compact('editoras'));
    }

    public function editorasCreate()
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }
        return view('editoras.create');
    }

    public function editorasStore(Request $request)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|max:2048'
        ]);

        $logotipoPath = null;
        if ($request->hasFile('logotipo')) {
            $logotipoPath = $request->file('logotipo')->store('editoras', 'public');
        }

        Editora::create([
            'nome' => $request->nome,
            'logotipo' => $logotipoPath,
        ]);

        return redirect()->route('editoras.index')->with('sucesso', 'Editora adicionada!');
    }

    public function editorasShow($id)
    {
        $editora = Editora::with('livros')->findOrFail($id);
        return view('editoras.show', compact('editora'));
    }

    public function editorasEdit($id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $editora = Editora::findOrFail($id);
        return view('editoras.edit', compact('editora'));
    }

    public function editorasUpdate(Request $request, $id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $editora = Editora::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('logotipo')) {
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }
            $editora->logotipo = $request->file('logotipo')->store('editoras', 'public');
        }

        $editora->update(['nome' => $request->nome]);
        return redirect()->route('editoras.index')->with('sucesso', 'Editora atualizada!');
    }

    public function editorasDestroy($id)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $editora = Editora::findOrFail($id);

        if ($editora->livros()->count() > 0) {
            return redirect()->route('editoras.index')->with('erro', 'Editora tem livros associados!');
        }

        if ($editora->logotipo) {
            Storage::disk('public')->delete($editora->logotipo);
        }

        $editora->delete();
        return redirect()->route('editoras.index')->with('sucesso', 'Editora removida!');
    }
}
