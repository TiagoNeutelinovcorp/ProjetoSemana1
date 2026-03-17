<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use PragmaRX\Google2FAQRCode\Google2FA;



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
        return view('auth.login-form', ['tipo' => 'cliente']);
    }

    public function loginBibliotecario()
    {
        return view('auth.login-form', ['tipo' => 'bibliotecario']);
    }

    public function loginStore(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'As credenciais não correspondem.',
            ])->onlyInput('email');
        }

        // Se o utilizador tem 2FA ativo
        if ($user->two_factor_secret) {
            session(['2fa:user:id' => $user->id]);
            return redirect()->route('two-factor.challenge');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended('/livros');
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
        ]);

        // Verificar se é o primeiro utilizador (ID=1 será bibliotecário)
        $isFirstUser = User::count() === 0;

        $role = $isFirstUser ? 'bibliotecario' : 'cliente';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        Auth::login($user);

        // Se for o primeiro utilizador, mostrar mensagem especial
        if ($isFirstUser) {
            return redirect('/livros')->with('sucesso', 'Conta de administrador criada com sucesso!');
        }

        return redirect('/livros')->with('sucesso', 'Registo realizado com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ==================== PERFIL DO UTILIZADOR ====================

    public function profileIndex()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function profilePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('sucesso', 'Password atualizada com sucesso!');
    }

    // ==================== 2FA (SEM CÓDIGOS DE RECUPERAÇÃO) ====================

    public function twoFactorEnable(Request $request)
    {
        $user = $request->user();

        if ($user->two_factor_secret) {
            return back()->with('erro', '2FA já está ativo.');
        }

        // Gerar chave secreta manualmente
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
        ])->save();

        return redirect()->route('profile.index')->with('sucesso', '2FA ativado com sucesso!');
    }

    public function twoFactorDisable(Request $request)
    {
        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
        ])->save();

        return redirect()->route('profile.index')->with('sucesso', '2FA desativado com sucesso!');
    }

    public function twoFactorVerify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa:user:id');

        if (!$userId) {
            return redirect()->route('login')->withErrors([
                'email' => 'Sessão expirada. Faça login novamente.',
            ]);
        }

        $user = User::find($userId);

        if (!$user || !$user->two_factor_secret) {
            return redirect()->route('login')->withErrors([
                'email' => 'Utilizador inválido.',
            ]);
        }

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code);

        if (!$valid) {
            return back()->withErrors([
                'code' => 'Código 2FA inválido.',
            ]);
        }

        Auth::login($user, true);
        session()->forget('2fa:user:id');
        $request->session()->regenerate();

        return redirect()->intended('/livros');
    }

    // ==================== GESTÃO DE UTILIZADORES ====================

    public function usersIndex(Request $request)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $query = User::query();

        if ($request->filled('pesquisa')) {
            $pesquisa = $request->pesquisa;
            $query->where(function($q) use ($pesquisa) {
                $q->where('name', 'like', "%{$pesquisa}%")
                    ->orWhere('email', 'like', "%{$pesquisa}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }


        $users = $query->orderBy('id')->paginate(10)->withQueryString();

        return view('profile.users.index', compact('users'));
    }

    public function usersUpdateRole(Request $request, User $user)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $request->validate([
            'role' => 'required|in:cliente,bibliotecario'
        ]);

        $user->update([
            'role' => $request->role
        ]);

        return back()->with('sucesso', 'Role atualizado com sucesso!');
    }

    public function usersDestroy(User $user)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        if ($user->id === auth()->id()) {
            return back()->with('erro', 'Não podes apagar a tua própria conta!');
        }

        $user->delete();

        return back()->with('sucesso', 'Utilizador apagado com sucesso!');
    }

    // ==================== LIVROS ====================

    public function livrosIndex(Request $request)
    {
        $query = Livro::with(['editora', 'autores']);

        if ($request->filled('pesquisa')) {
            $pesquisa = $request->pesquisa;
            $query->where(function($q) use ($pesquisa) {
                $q->where('nome', 'like', "%{$pesquisa}%")
                    ->orWhere('isbn', 'like', "%{$pesquisa}%");
            });
        }

        if ($request->filled('autor')) {
            $query->whereHas('autores', function($q) use ($request) {
                $q->where('autores.id', $request->autor);
            });
        }

        if ($request->filled('editora')) {
            $query->where('editora_id', $request->editora);
        }

        $livros = $query->latest()->paginate(8)->withQueryString();
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

        // Carregar reviews ativas com paginação
        $reviews = $livro->reviewsAtivos()
            ->with('user')
            ->latest()
            ->paginate(7);

        // LIVROS RELACIONADOS usando palavras-chave
        $livrosRelacionados = collect();

        if (!empty($livro->bibliografia)) {
            // Extrair termos relevantes da descrição
            $termos = $this->extrairTermosBusca($livro->bibliografia);

            if (!empty($termos)) {
                // Buscar livros relacionados
                $livrosRelacionados = $this->buscarLivrosRelacionados($livro, $termos);
            }
        }

        return view('livros.show', compact('livro', 'reviews', 'livrosRelacionados'));
    }

    /**
     * Buscar livros relacionados por palavras-chave
     */
    private function buscarLivrosRelacionados(Livro $livro, array $termos)
    {
        if (empty($termos)) {
            return collect();
        }

        $query = Livro::with(['autores', 'editora'])
            ->where('id', '!=', $livro->id)
            ->whereNotNull('bibliografia')
            ->where('bibliografia', '!=', '');

        // Construir busca por palavras-chave
        $query->where(function($q) use ($termos) {
            foreach ($termos as $termo) {
                if (strlen($termo) > 3) {
                    $q->orWhere('bibliografia', 'LIKE', "%{$termo}%")
                        ->orWhere('nome', 'LIKE', "%{$termo}%");
                }
            }
        });

        $candidatos = $query->get();

        if ($candidatos->isEmpty()) {
            return collect();
        }

        // Calcular score para cada candidato
        $candidatosComScore = $candidatos->map(function($candidato) use ($termos) {
            $score = 0;
            $textoCompleto = ($candidato->nome ?? '') . ' ' . ($candidato->bibliografia ?? '');
            $textoCompleto = mb_strtolower($this->removerAcentos($textoCompleto));

            foreach ($termos as $termo) {
                $termoLower = mb_strtolower($termo);
                $score += substr_count($textoCompleto, $termoLower) * 2;
            }

            $candidato->score = $score;
            return $candidato;
        });

        // Filtrar com score mínimo e ordenar
        return $candidatosComScore
            ->filter(fn($c) => $c->score > 0)
            ->sortByDesc('score')
            ->take(3)
            ->values();
    }

    /**
     * Extrair termos relevantes da descrição
     */
    private function extrairTermosBusca(string $texto): array
    {
        // Remover tags HTML se houver
        $texto = strip_tags($texto);

        // Converter para minúsculas e remover acentos
        $texto = $this->removerAcentos(mb_strtolower($texto));

        // Remover pontuação e caracteres especiais
        $texto = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $texto);

        // Lista de stop words em português
        $stopWords = [
            'livro', 'sobre', 'este', 'esta', 'estes', 'estas', 'com', 'para',
            'uma', 'umas', 'como', 'mais', 'muito', 'pode', 'ser', 'quando',
            'tambem', 'ainda', 'entre', 'apos', 'antes', 'durante', 'sem',
            'historia', 'obra', 'autor', 'autores', 'editora', 'pagina',
            'capitulo', 'romance', 'conto', 'texto', 'descricao', 'tem', 'seus',
            'sua', 'suas', 'seu', 'meu', 'minha', 'nosso', 'nossa', 'deste',
            'desta', 'destes', 'destas', 'nesse', 'neste', 'nessa', 'nesta',
            'aquele', 'aquela', 'aqueles', 'aquelas', 'isso', 'isto', 'aquilo',
            'porque', 'pois', 'porem', 'portanto', 'assim', 'entao', 'enquanto'
        ];

        // Dividir em palavras
        $palavras = preg_split('/\s+/', $texto, -1, PREG_SPLIT_NO_EMPTY);

        // Filtrar stop words e palavras curtas
        $palavras = array_filter($palavras, function($palavra) use ($stopWords) {
            return strlen($palavra) > 3 && !in_array($palavra, $stopWords);
        });

        // Contar frequência das palavras
        $frequencia = array_count_values($palavras);

        // Ordenar por frequência (mais frequentes primeiro)
        arsort($frequencia);

        // Pegar as 8 palavras mais frequentes
        $termos = array_slice(array_keys($frequencia), 0, 8);

        return $termos;
    }

    /**
     * Remover acentos de uma string
     */
    private function removerAcentos(string $texto): string
    {
        $acentos = [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
        ];

        return strtr($texto, $acentos);
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


// ==================== FOTO DE PERFIL ====================

    public function profileUpdatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|max:2048', // max 2MB
        ]);

        $user = Auth::user();

        // Apagar foto antiga se existir
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Guardar nova foto
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update([
            'profile_photo' => $path
        ]);

        return back()->with('sucesso', 'Foto de perfil atualizada com sucesso!');
    }

    public function profileDeletePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return back()->with('sucesso', 'Foto de perfil removida com sucesso!');
    }


}
