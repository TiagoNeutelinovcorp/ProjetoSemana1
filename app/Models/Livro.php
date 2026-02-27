<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    use HasFactory;

    protected $table = 'livros';

    protected $fillable = [
        'isbn',
        'nome',
        'bibliografia',
        'imagem_capa',
        'preco',
        'editora_id',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    public function editora()
    {
        return $this->belongsTo(Editora::class, 'editora_id');
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro', 'livro_id', 'autor_id');
    }

    public function getImagemCapaUrlAttribute()
    {
        return $this->imagem_capa ? asset('storage/' . $this->imagem_capa) : 'https://via.placeholder.com/300x400?text=Sem+Capa';
    }

    public function getPrecoFormatadoAttribute()
    {
        return '€ ' . number_format($this->preco, 2, ',', '.');
    }
}
