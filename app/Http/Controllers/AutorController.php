<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Autor::with('livros.editora');

        // Filtro por pesquisa (nome)
        if ($request->filled('pesquisa')) {
            $query->where('nome', 'like', '%' . $request->pesquisa . '%');
        }

        $autores = $query->orderBy('nome')->paginate(8)->withQueryString();

        return view('autores.autores', compact('autores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('autores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('autores', 'public');
        }

        Autor::create([
            'nome' => $request->nome,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('autores.index')->with('sucesso', 'Autor adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $autor = Autor::with('livros')->findOrFail($id);
        return view('autores.show', compact('autor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $autor = Autor::findOrFail($id);
        return view('autores.edit', compact('autor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $autor = Autor::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($autor->foto) {
                Storage::disk('public')->delete($autor->foto);
            }
            $autor->foto = $request->file('foto')->store('autores', 'public');
        }

        $autor->nome = $request->nome;
        $autor->save();

        return redirect()->route('autores.index')->with('sucesso', 'Autor atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $autor = Autor::findOrFail($id);

        if ($autor->livros()->count() > 0) {
            return redirect()->route('autores.index')->with('erro', 'Não é possível apagar este autor porque tem livros associados.');
        }

        if ($autor->foto) {
            Storage::disk('public')->delete($autor->foto);
        }
        $autor->delete();

        return redirect()->route('autores.index')->with('sucesso', 'Autor removido com sucesso!');
    }
}
