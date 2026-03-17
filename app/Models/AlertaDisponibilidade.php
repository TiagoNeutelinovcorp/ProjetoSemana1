<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaDisponibilidade extends Model
{
    use HasFactory;

    protected $table = 'alertas_disponibilidade';

    protected $fillable = [
        'user_id',
        'livro_id',
        'status',
        'notificado_em',
    ];

    protected $casts = [
        'notificado_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }
}
