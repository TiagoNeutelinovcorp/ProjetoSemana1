<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\LivroSugestao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NovaSugestaoLivro;

class GoogleBooksController extends Controller
{
    /**
     * Mostrar o formulário de pesquisa
     */
    public function showSearchForm()
    {
        return view('google-books.search');
    }

    /**
     * Pesquisar livros na Google Books API
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $query = $request->input('query');
        $page = $request->input('page', 1);
        $maxResults = 10;
        $startIndex = ($page - 1) * $maxResults;

        $apiKey = env('GOOGLE_BOOKS_API_KEY');

        try {
            $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                'q' => $query,
                'key' => $apiKey,
                'maxResults' => $maxResults,
                'startIndex' => $startIndex,
                'langRestrict' => 'pt',
            ]);

            if ($response->failed()) {
                \Log::error('Google Books API error: Status ' . $response->status());
                return back()->with('erro', 'Erro na API da Google (Código: ' . $response->status() . '). Tenta novamente.')
                    ->withInput();
            }

            $data = $response->json();
            $booksData = $data['items'] ?? [];
            $totalItems = $data['totalItems'] ?? 0;
            $totalPages = ceil($totalItems / $maxResults);

            $books = [];

            foreach ($booksData as $item) {
                $volumeInfo = $item['volumeInfo'];
                $saleInfo = $item['saleInfo'] ?? [];

                // Extrair preço real (retailPrice)
                $preco = 0;
                $moeda = 'EUR';

                if (isset($saleInfo['saleability']) && $saleInfo['saleability'] === 'FOR_SALE') {
                    // Dar prioridade ao retailPrice (preço real)
                    if (isset($saleInfo['retailPrice'])) {
                        $preco = $saleInfo['retailPrice']['amount'] ?? 0;
                        $moeda = $saleInfo['retailPrice']['currencyCode'] ?? 'EUR';
                    }
                    // Se não tiver retailPrice, usar listPrice
                    elseif (isset($saleInfo['listPrice'])) {
                        $preco = $saleInfo['listPrice']['amount'] ?? 0;
                        $moeda = $saleInfo['listPrice']['currencyCode'] ?? 'EUR';
                    }
                }

                $books[] = [
                    'google_books_id' => $item['id'],
                    'titulo' => $volumeInfo['title'] ?? 'Título desconhecido',
                    'autores' => $volumeInfo['authors'] ?? ['Autor desconhecido'],
                    'editora' => $volumeInfo['publisher'] ?? 'Editora desconhecida',
                    'descricao' => $volumeInfo['description'] ?? null,
                    'isbn' => $this->extractIsbn($volumeInfo['industryIdentifiers'] ?? []),
                    'capa_thumbnail' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
                    'capa_pequena' => $volumeInfo['imageLinks']['smallThumbnail'] ?? null,
                    'capa_grande' => str_replace('zoom=1', 'zoom=2', $volumeInfo['imageLinks']['thumbnail'] ?? ''),
                    'paginas' => $volumeInfo['pageCount'] ?? null,
                    'data_publicacao' => $volumeInfo['publishedDate'] ?? null,
                    'preco' => $preco,
                    'moeda' => $moeda,
                    'preco_disponivel' => $preco > 0,
                ];
            }

            return view('google-books.search-results', compact('books', 'query', 'page', 'totalPages', 'totalItems'));

        } catch (\Exception $e) {
            \Log::error('Exceção ao chamar Google Books API: ' . $e->getMessage());
            return back()->with('erro', 'Ocorreu um erro inesperado. Tenta novamente.')
                ->withInput();
        }
    }

    /**
     * Importar um livro selecionado (ADMIN - importação direta)
     */
    public function import(Request $request)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado. Apenas bibliotecários podem importar livros diretamente.');
        }

        $validated = $this->validateLivro($request);

        // Verificar se o livro já existe pelo ISBN
        if ($validated['isbn']) {
            $livroExistente = Livro::where('isbn', $validated['isbn'])->first();
            if ($livroExistente) {
                return redirect()->route('livros.index')
                    ->with('erro', "O livro '{$livroExistente->nome}' já existe na base de dados.");
            }
        }

        return $this->processarImportacao($validated);
    }

    /**
     * Sugerir um livro (CLIENTE - envia para aprovação)
     */
    public function sugerir(Request $request)
    {
        $validated = $this->validateLivro($request);

        // Verificar se o livro já existe pelo ISBN
        if ($validated['isbn']) {
            $livroExistente = Livro::where('isbn', $validated['isbn'])->first();
            if ($livroExistente) {
                return redirect()->route('livros.index')
                    ->with('erro', "Este livro já existe na biblioteca!");
            }
        }

        // Verificar se já existe uma sugestão pendente para este ISBN
        if ($validated['isbn']) {
            $sugestaoExistente = LivroSugestao::where('isbn', $validated['isbn'])
                ->where('status', 'pendente')
                ->first();

            if ($sugestaoExistente) {
                return redirect()->route('livros.index')
                    ->with('erro', "Já existe uma sugestão pendente para este livro.");
            }
        }

        // Criar sugestão
        $sugestao = LivroSugestao::create([
            'user_id' => Auth::id(),
            'titulo' => $validated['titulo'],
            'autores' => $validated['autores'],
            'editora' => $validated['editora'],
            'descricao' => $validated['descricao'],
            'isbn' => $validated['isbn'],
            'capa_thumbnail' => $validated['capa_thumbnail'],
            'capa_grande' => $validated['capa_grande'],
            'paginas' => $validated['paginas'] ?? null,
            'data_publicacao' => $validated['data_publicacao'] ?? null,
            'preco' => $validated['preco'] ?? 0,
            'status' => 'pendente',
        ]);

        // Notificar todos os administradores
        try {
            $admins = User::where('role', 'bibliotecario')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NovaSugestaoLivro($sugestao));
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao notificar admins sobre nova sugestão: ' . $e->getMessage());
        }

        return redirect()->route('livros.index')
            ->with('sucesso', 'Livro sugerido com sucesso! Aguarda aprovação de um administrador.');
    }

    /**
     * Validar dados do livro
     */
    private function validateLivro(Request $request)
    {
        return $request->validate([
            'titulo' => 'required|string|max:255',
            'autores' => 'required|string',
            'editora' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'isbn' => 'nullable|string',
            'capa_thumbnail' => 'nullable|string',
            'capa_grande' => 'nullable|string',
            'paginas' => 'nullable|integer',
            'data_publicacao' => 'nullable|string',
            'preco' => 'nullable|numeric|min:0',
        ]);
    }

    /**
     * Processar importação (cria livro, editora, autores)
     */
    private function processarImportacao($dados, $sugestaoId = null)
    {
        try {
            // 1. Processar Editora
            $editora = Editora::firstOrCreate(['nome' => $dados['editora']]);

            // 2. Processar Autores
            $autoresNomes = array_map('trim', explode(',', $dados['autores']));
            $autoresIds = [];

            foreach ($autoresNomes as $nome) {
                if (!empty($nome)) {
                    $autor = Autor::firstOrCreate(['nome' => $nome]);
                    $autoresIds[] = $autor->id;
                }
            }

            // 3. Download da imagem da capa
            $imagemPath = null;
            $imagemUrl = $dados['capa_grande'] ?? $dados['capa_thumbnail'] ?? null;

            if ($imagemUrl) {
                try {
                    $imagemUrl = str_replace('&edge=curl', '', $imagemUrl);
                    $imageContents = file_get_contents($imagemUrl);
                    $imageName = 'livros/' . uniqid() . '.jpg';
                    Storage::disk('public')->put($imageName, $imageContents);
                    $imagemPath = $imageName;
                } catch (\Exception $e) {
                    \Log::warning('Falha ao baixar imagem: ' . $e->getMessage());
                }
            }

            // 4. Determinar o preço (usar preço da API ou 0 se não disponível)
            $preco = $dados['preco'] ?? 0;
            if ($preco === null || $preco < 0) {
                $preco = 0;
            }

            // 5. Criar o Livro
            $livro = Livro::create([
                'isbn' => $dados['isbn'],
                'nome' => $dados['titulo'],
                'bibliografia' => $dados['descricao'],
                'preco' => $preco,
                'editora_id' => $editora->id,
                'imagem_capa' => $imagemPath,
            ]);

            // 6. Associar Autores
            $livro->autores()->sync($autoresIds);

            // 7. Se veio de uma sugestão, atualizar status
            if ($sugestaoId) {
                $sugestao = LivroSugestao::find($sugestaoId);
                if ($sugestao) {
                    $sugestao->update([
                        'status' => 'aprovado',
                        'aprovado_em' => now(),
                        'aprovado_por' => Auth::id(),
                    ]);
                }
            }

            return redirect()->route('livros.index')
                ->with('sucesso', "Livro '{$livro->nome}' importado com sucesso!");

        } catch (\Exception $e) {
            return back()->with('erro', 'Erro ao importar livro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Listar sugestões pendentes (admin)
     */
    public function listarSugestoes()
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado. Apenas bibliotecários podem ver sugestões.');
        }

        $sugestoes = LivroSugestao::with('user')
            ->orderByRaw("CASE status WHEN 'pendente' THEN 1 WHEN 'aprovado' THEN 2 WHEN 'rejeitado' THEN 3 ELSE 4 END")
            ->latest()
            ->paginate(15);

        return view('google-books.sugestoes', compact('sugestoes'));
    }

    /**
     * Aprovar sugestão (admin)
     */
    public function aprovarSugestao(LivroSugestao $sugestao)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado. Apenas bibliotecários podem aprovar sugestões.');
        }

        if ($sugestao->status !== 'pendente') {
            return back()->with('erro', 'Esta sugestão já foi processada.');
        }

        // Verificar se o livro já existe
        if ($sugestao->isbn) {
            $livroExistente = Livro::where('isbn', $sugestao->isbn)->first();
            if ($livroExistente) {
                $sugestao->update([
                    'status' => 'rejeitado',
                    'observacoes_admin' => 'Livro já existe na base de dados',
                ]);
                return back()->with('erro', 'Este livro já existe na base de dados.');
            }
        }

        // Preparar dados para importação
        $dados = [
            'titulo' => $sugestao->titulo,
            'autores' => $sugestao->autores,
            'editora' => $sugestao->editora,
            'descricao' => $sugestao->descricao,
            'isbn' => $sugestao->isbn,
            'capa_thumbnail' => $sugestao->capa_thumbnail,
            'capa_grande' => $sugestao->capa_grande,
            'paginas' => $sugestao->paginas,
            'data_publicacao' => $sugestao->data_publicacao,
            'preco' => $sugestao->preco ?? 0,
        ];

        return $this->processarImportacao($dados, $sugestao->id);
    }

    /**
     * Rejeitar sugestão (admin)
     */
    public function rejeitarSugestao(Request $request, LivroSugestao $sugestao)
    {
        if (!auth()->user()->isBibliotecario()) {
            abort(403, 'Acesso negado. Apenas bibliotecários podem rejeitar sugestões.');
        }

        $request->validate([
            'motivo' => 'required|string|max:255',
        ]);

        $sugestao->update([
            'status' => 'rejeitado',
            'observacoes_admin' => $request->motivo,
        ]);

        return back()->with('sucesso', 'Sugestão rejeitada com sucesso.');
    }

    /**
     * Extrair ISBN
     */
    private function extractIsbn($identifiers)
    {
        if (empty($identifiers)) {
            return null;
        }

        foreach ($identifiers as $identifier) {
            if ($identifier['type'] === 'ISBN_13') {
                return $identifier['identifier'];
            }
        }

        foreach ($identifiers as $identifier) {
            if ($identifier['type'] === 'ISBN_10') {
                return $identifier['identifier'];
            }
        }

        return null;
    }
}
