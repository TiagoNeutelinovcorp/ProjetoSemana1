<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'modulo',
        'objeto_id',
        'acao',
        'alteracao',
        'ip',
        'browser',
    ];

    protected $casts = [
        'alteracao' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor para data formatada
    public function getDataAttribute(): string
    {
        return $this->created_at->format('d/m/Y');
    }

    // Accessor para hora formatada
    public function getHoraAttribute(): string
    {
        return $this->created_at->format('H:i:s');
    }

    // ===== NOVO: Melhorar exibição da alteração =====

    /**
     * Retorna a alteração formatada para exibição
     */
    public function getAlteracaoFormatadaAttribute(): string
    {
        $alteracao = $this->alteracao;

        if (is_string($alteracao)) {
            $alteracao = json_decode($alteracao, true);
        }

        if (!$alteracao || !is_array($alteracao)) {
            return '—';
        }

        $html = '<ul class="list-unstyled mb-0">';
        foreach ($alteracao as $campo => $valor) {
            $campoTraduzido = $this->traduzirCampo($campo);

            if (is_array($valor)) {
                $valor = implode(', ', $valor);
            }

            $html .= '<li><strong>' . $campoTraduzido . ':</strong> ' . e($valor) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * Retorna a alteração em texto simples
     */
    public function getAlteracaoTextoAttribute(): string
    {
        $alteracao = $this->alteracao;

        if (is_string($alteracao)) {
            $alteracao = json_decode($alteracao, true);
        }

        if (!$alteracao || !is_array($alteracao)) {
            return '—';
        }

        $texto = [];
        foreach ($alteracao as $campo => $valor) {
            $campoTraduzido = $this->traduzirCampo($campo);

            if (is_array($valor)) {
                $valor = implode(', ', $valor);
            }

            $texto[] = $campoTraduzido . ': ' . $valor;
        }

        return implode(' | ', $texto);
    }

    /**
     * Traduz os nomes dos campos para português
     */
    private function traduzirCampo(string $campo): string
    {
        $traducoes = [
            'livro' => 'Livro',
            'data_prevista' => 'Data Prevista',
            'dias_atraso' => 'Dias em Atraso',
            'stock' => 'Stock',
            'status' => 'Estado',
            'user_id' => 'Utilizador',
            'livro_id' => 'Livro',
        ];

        return $traducoes[$campo] ?? ucfirst($campo);
    }
}
