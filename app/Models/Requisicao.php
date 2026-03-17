<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisicao extends Model
{
    use HasFactory;

    protected $table = 'requisicoes';
    protected $fillable = [
        'codigo',
        'user_id',
        'livro_id',
        'data_requisicao',
        'data_prevista_devolucao',
        'data_devolucao_real',
        'status',
        'observacoes',
        'dias_atraso',
    ];

    protected $casts = [
        'data_requisicao' => 'datetime',
        'data_prevista_devolucao' => 'datetime',
        'data_devolucao_real' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function isAtrasada()
    {
        return $this->status === 'ativo' && now()->gt($this->data_prevista_devolucao);
    }

    public function calcularDiasAtraso()
    {
        if ($this->status === 'ativo' && $this->isAtrasada()) {
            return now()->diffInDays($this->data_prevista_devolucao);
        }

        if ($this->data_devolucao_real && $this->data_devolucao_real->gt($this->data_prevista_devolucao)) {
            return $this->data_prevista_devolucao->diffInDays($this->data_devolucao_real);
        }

        return 0;
    }

    public function review()
    {
        return $this->hasOne(Review::class, 'requisicao_id');
    }
}

