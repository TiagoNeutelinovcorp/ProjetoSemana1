<?php

namespace App\Http\Controllers;

use App\Models\AlertaDisponibilidade;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertaDisponibilidadeController extends Controller
{
    /**
     * Criar um alerta para ser notificado quando o livro ficar disponível
     */
    public function store(Request $request, Livro $livro)
    {
        $user = Auth::user();

        // Verificar se o livro está realmente indisponível
        if ($livro->isDisponivel()) {
            return redirect()->route('livros.show', $livro)
                ->with('erro', 'Este livro já está disponível. Podes requisitá-lo agora!');
        }

        // VERIFICAÇÃO MAIS ROBUSTA
        $alertaExistente = AlertaDisponibilidade::where('user_id', $user->id)
            ->where('livro_id', $livro->id)
            ->whereIn('status', ['pendente', 'enviado']) // Considerar também enviados? Talvez só pendentes
            ->first();

        if ($alertaExistente) {
            $mensagem = $alertaExistente->status === 'pendente'
                ? 'Já tens um alerta ativo para este livro. Serás notificado quando ficar disponível.'
                : 'Já foste notificado para este livro anteriormente.';

            return redirect()->route('livros.show', $livro)
                ->with('erro', $mensagem);
        }

        // Criar novo alerta
        try {
            $alerta = AlertaDisponibilidade::create([
                'user_id' => $user->id,
                'livro_id' => $livro->id,
                'status' => 'pendente',
            ]);
        } catch (\Exception $e) {
            // Se mesmo assim der erro, capturar e mostrar mensagem amigável
            return redirect()->route('livros.show', $livro)
                ->with('erro', 'Ocorreu um erro ao criar o alerta. Já deves ter um alerta ativo para este livro.');
        }

        return redirect()->route('livros.show', $livro)
            ->with('sucesso', 'Alerta criado! Serás notificado por email quando este livro ficar disponível.');
    }

    /**
     * Cancelar um alerta
     */
    public function destroy(AlertaDisponibilidade $alerta)
    {
        if ($alerta->user_id !== Auth::id()) {
            abort(403);
        }

        if ($alerta->status === 'pendente') {
            $alerta->update(['status' => 'cancelado']);
        }

        return back()->with('sucesso', 'Alerta cancelado com sucesso.');
    }

    /**
     * Listar alertas do utilizador
     */
    public function index()
    {
        $alertas = AlertaDisponibilidade::with('livro')
            ->where('user_id', Auth::id())
            ->where('status', 'pendente')
            ->latest()
            ->paginate(10);

        return view('alertas.index', compact('alertas'));
    }
}
