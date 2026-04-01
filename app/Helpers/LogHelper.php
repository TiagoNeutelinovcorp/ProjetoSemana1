<?php

namespace App\Helpers;

use App\Models\Log;
use Illuminate\Support\Facades\Request;

class LogHelper
{
    /**
     * Registrar uma ação no log do sistema
     *
     * @param string $modulo
     * @param string $acao
     * @param int|null $objetoId
     * @param array|null $alteracao
     * @return void
     */
    public static function registrar($modulo, $acao, $objetoId = null, $alteracao = null)
    {
        try {
            Log::create([
                'user_id' => auth()->id(),
                'modulo' => $modulo,
                'objeto_id' => $objetoId,
                'acao' => $acao,
                'alteracao' => $alteracao ? json_encode($alteracao) : null,
                'ip' => Request::ip(),
                'browser' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Não deixar o log quebrar a aplicação
            \Log::error('Erro ao registrar log: ' . $e->getMessage());
        }
    }
}
