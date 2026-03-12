<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivroSugestao extends Model
{
    use HasFactory;

    protected $table = 'livros_sugestoes';

    protected $fillable = [
        'user_id',
        'titulo',
        'autores',
        'editora',
        'descricao',
        'isbn',
        'capa_thumbnail',
        'capa_grande',
        'paginas',
        'data_publicacao',
        'status',
        'observacoes_admin',
        'aprovado_em',
        'aprovado_por',
    ];

    protected $casts = [
        'aprovado_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aprovador()
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    public function getCapaUrlAttribute()
    {
        return $this->capa_grande ?? $this->capa_thumbnail ?? null;
    }
}
