<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encomenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_encomenda',
        'subtotal',
        'iva',
        'total',
        'status',
        'metodo_pagamento',
        'stripe_payment_intent_id',
        'stripe_payment_method_id',
        'morada',
        'cidade',
        'codigo_postal',
        'pais',
        'telefone',
        'pago_em',
        'enviado_em',
        'entregue_em',
    ];

    protected $casts = [
        'pago_em' => 'datetime',
        'enviado_em' => 'datetime',
        'entregue_em' => 'datetime',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(EncomendaItem::class);
    }

    public function historico()
    {
        return $this->hasMany(EncomendaHistorico::class);
    }
}
