<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'requisicao_id',
        'user_id',
        'livro_id',
        'rating',
        'comentario',
        'status',
        'justificacao_recusa',
        'aprovado_em',
        'aprovado_por',
    ];

    protected $casts = [
        'aprovado_em' => 'datetime',
        'rating' => 'integer',
    ];

    public function requisicao()
    {
        return $this->belongsTo(Requisicao::class, 'requisicao_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'livro_id');
    }

    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'suspenso');
    }
}
