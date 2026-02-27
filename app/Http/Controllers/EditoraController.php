<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Editora::with('livros.autores');

        // Filtro por pesquisa (nome)
        if ($request->filled('pesquisa')) {
            $query->where('nome', 'like', '%' . $request->pesquisa . '%');
        }

        $editoras = $query->orderBy('nome')->paginate(8)->withQueryString();

        return view('editoras.editoras', compact('editoras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('editoras.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logotipoPath = null;
        if ($request->hasFile('logotipo')) {
            $logotipoPath = $request->file('logotipo')->store('editoras', 'public');
        }

        Editora::create([
            'nome' => $request->nome,
            'logotipo' => $logotipoPath,
        ]);

        return redirect()->route('editoras.index')->with('sucesso', 'Editora adicionada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $editora = Editora::with('livros.autores')->findOrFail($id);
        return view('editoras.show', compact('editora'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $editora = Editora::findOrFail($id);
        return view('editoras.edit', compact('editora'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $editora = Editora::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'logotipo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logotipo')) {
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }
            $editora->logotipo = $request->file('logotipo')->store('editoras', 'public');
        }

        $editora->nome = $request->nome;
        $editora->save();

        return redirect()->route('editoras.index')->with('sucesso', 'Editora atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $editora = Editora::findOrFail($id);

        if ($editora->livros()->count() > 0) {
            return redirect()->route('editoras.index')->with('erro', 'Não é possível apagar esta editora porque tem livros associados.');
        }

        if ($editora->logotipo) {
            Storage::disk('public')->delete($editora->logotipo);
        }
        $editora->delete();

        return redirect()->route('editoras.index')->with('sucesso', 'Editora removida com sucesso!');
    }
}
