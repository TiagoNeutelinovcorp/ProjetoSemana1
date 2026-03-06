<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'nome',
        'bibliografia',
        'preco',
        'editora_id',
        'imagem_capa',
    ];

    protected $appends = ['imagem_capa_url', 'preco_formatado'];

    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro');
    }

    public function getImagemCapaUrlAttribute()
    {
        return $this->imagem_capa
            ? asset('storage/' . $this->imagem_capa)
            : 'https://via.placeholder.com/150x200?text=Sem+Imagem';
    }

    public function getPrecoFormatadoAttribute()
    {
        return '€ ' . number_format($this->preco, 2, ',', '.');
    }

    // ==================== REQUISIÇÕES ====================

    public function requisicoes()
    {
        return $this->hasMany(Requisicao::class);
    }

    public function isDisponivel()
    {
        return !$this->requisicoes()
            ->whereIn('status', ['ativo', 'devolucao_solicitada'])
            ->exists();
    }

    public function requisicaoAtiva()
    {
        return $this->requisicoes()
            ->whereIn('status', ['ativo', 'devolucao_solicitada'])
            ->latest()
            ->first();
    }
}
