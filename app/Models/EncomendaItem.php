<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncomendaItem extends Model
{
    use HasFactory;

    protected $table = 'encomenda_itens';  // ← ADICIONA ESTA LINHA

    protected $fillable = [
        'encomenda_id',
        'livro_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
    ];

    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }
}
