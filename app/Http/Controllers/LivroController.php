<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LivroController extends Controller
{
    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request)
    {
        $query = Livro::with(['editora', 'autores']);

        // Filtro por pesquisa (nome ou ISBN)
        if ($request->filled('pesquisa')) {
            $query->where(function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->pesquisa . '%')
                    ->orWhere('isbn', 'like', '%' . $request->pesquisa . '%');
            });
        }

        // Filtro por autor
        if ($request->filled('autor')) {
            $query->whereHas('autores', function($q) use ($request) {
                $q->where('autores.id', $request->autor);
            });
        }

        // Filtro por editora
        if ($request->filled('editora')) {
            $query->where('editora_id', $request->editora);
        }

        $livros = $query->latest()->paginate(9)->withQueryString();

        // Buscar autores e editoras para os selects
        $autores = Autor::orderBy('nome')->get();
        $editoras = Editora::orderBy('nome')->get();

        return view('livros.livros', compact('livros', 'autores', 'editoras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Buscar todas as editoras e autores ordenados por nome
        $editoras = Editora::orderBy('nome')->get();
        $autores = Autor::orderBy('nome')->get();

        return view('livros.create', compact('editoras', 'autores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'isbn' => 'required|string|unique:livros,isbn|max:20',
            'nome' => 'required|string|max:255',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'editora_id' => 'required|exists:editoras,id',
            'autor_id' => 'required|exists:autores,id',
            'imagem_capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'isbn.required' => 'O ISBN é obrigatório',
            'isbn.unique' => 'Este ISBN já está cadastrado',
            'nome.required' => 'O nome do livro é obrigatório',
            'preco.required' => 'O preço é obrigatório',
            'editora_id.required' => 'Selecione uma editora',
            'autor_id.required' => 'Selecione um autor',
            'imagem_capa.image' => 'O ficheiro deve ser uma imagem',
            'imagem_capa.max' => 'A imagem não pode ter mais de 2MB',
        ]);

        // Upload da imagem
        $imagemPath = null;
        if ($request->hasFile('imagem_capa')) {
            $imagemPath = $request->file('imagem_capa')->store('livros', 'public');
        }

        // Criar o livro
        $livro = Livro::create([
            'isbn' => $request->isbn,
            'nome' => $request->nome,
            'bibliografia' => $request->bibliografia,
            'imagem_capa' => $imagemPath,
            'preco' => $request->preco,
            'editora_id' => $request->editora_id,
        ]);

        // Associar o autor ao livro (como array de um elemento)
        $livro->autores()->attach([$request->autor_id]);

        return redirect()->route('livros.index')
            ->with('sucesso', 'Livro adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Livro $livro)
    {
        // Carregar relacionamentos
        $livro->load(['editora', 'autores']);

        return view('livros.show', compact('livro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Livro $livro)
    {
        // Buscar editoras e autores para os selects
        $editoras = Editora::orderBy('nome')->get();
        $autores = Autor::orderBy('nome')->get();

        // Buscar o ID do autor associado (como só tem 1)
        $autorSelecionado = $livro->autores->first()->id ?? null;

        return view('livros.edit', compact('livro', 'editoras', 'autores', 'autorSelecionado'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Livro $livro)
    {
        // Validação dos dados
        $request->validate([
            'isbn' => 'required|string|max:20|unique:livros,isbn,' . $livro->id,
            'nome' => 'required|string|max:255',
            'bibliografia' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'editora_id' => 'required|exists:editoras,id',
            'autor_id' => 'required|exists:autores,id',
            'imagem_capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Upload da nova imagem (se enviada)
        if ($request->hasFile('imagem_capa')) {
            // Apagar imagem antiga se existir
            if ($livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }

            $imagemPath = $request->file('imagem_capa')->store('livros', 'public');
            $livro->imagem_capa = $imagemPath;
        }

        // Atualizar dados
        $livro->isbn = $request->isbn;
        $livro->nome = $request->nome;
        $livro->bibliografia = $request->bibliografia;
        $livro->preco = $request->preco;
        $livro->editora_id = $request->editora_id;
        $livro->save();

        // Sincronizar autor (remove o antigo e adiciona o novo) - como array de um elemento
        $livro->autores()->sync([$request->autor_id]);

        return redirect()->route('livros.index')
            ->with('sucesso', 'Livro atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Livro $livro)
    {
        // Apagar imagem se existir
        if ($livro->imagem_capa) {
            Storage::disk('public')->delete($livro->imagem_capa);
        }

        // Remover relação com autor
        $livro->autores()->detach();

        // Apagar livro
        $livro->delete();

        return redirect()->route('livros.index')
            ->with('sucesso', 'Livro removido com sucesso!');
    }
}
