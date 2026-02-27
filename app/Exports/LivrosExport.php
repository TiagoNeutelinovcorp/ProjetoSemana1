<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LivrosExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Livro::with(['editora', 'autores'])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ISBN',
            'Título',
            'Preço',
            'Editora',
            'Autores',
            'Data de Cadastro',
        ];
    }

    /**
     * @param mixed $livro
     * @return array
     */
    public function map($livro): array
    {
        return [
            $livro->isbn,
            $livro->nome,
            '€ ' . number_format($livro->preco, 2, ',', '.'),
            $livro->editora->nome ?? 'N/A',
            $livro->autores->pluck('nome')->implode(', '),
            $livro->created_at->format('d/m/Y H:i'),
        ];
    }
}
