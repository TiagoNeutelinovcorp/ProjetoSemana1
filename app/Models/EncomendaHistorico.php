<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncomendaHistorico extends Model
{
    use HasFactory;

    protected $table = 'encomenda_historico';

    protected $fillable = [
        'encomenda_id',
        'user_id',
        'estado_anterior',
        'estado_novo',
        'observacoes',
    ];

    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
