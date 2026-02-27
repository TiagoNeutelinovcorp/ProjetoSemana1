<?php

namespace App\Exports;

use App\Models\Livro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LivrosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Livro::with(['editora', 'autores'])->get();
    }

    public function map($livro): array
    {
        return [
            $livro->isbn,
            $livro->nome,
            number_format($livro->preco, 2, ',', '.') . ' €',
            $livro->editora->nome ?? 'Sem editora',
            $livro->autores->pluck('nome')->implode(', '),
            $livro->created_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'ISBN',
            'TÍTULO',
            'PREÇO',
            'EDITORA',
            'AUTORES',
            'DATA',
        ];
    }
}
