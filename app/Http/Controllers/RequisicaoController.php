<?php

namespace App\Http\Controllers;

use App\Models\Requisicao;
use App\Models\Livro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\RequisicaoConfirmadaCidadao;
use App\Notifications\NovaRequisicaoAdmin;
use App\Notifications\SolicitacaoDevolucaoAdmin;


class RequisicaoController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isBibliotecario()) {
            abort(403, 'Acesso negado');
        }

        $query = Requisicao::with(['user', 'livro']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pesquisa por nome do cidadão
        if ($request->filled('cidadao')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->cidadao . '%');
            });
        }

        // Pesquisa por nome do livro
        if ($request->filled('livro')) {
            $query->whereHas('livro', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->livro . '%');
            });
        }

        $requisicoes = $query->latest()->paginate(10);

        $estatisticas = [
            'ativas' => Requisicao::whereIn('status', ['ativo', 'pendente'])->count(),
            'ultimos_30_dias' => Requisicao::where('created_at', '>=', now()->subDays(30))->count(),
            'entregues_hoje' => Requisicao::whereDate('data_devolucao_real', today())->count(),
        ];

        return view('requisicoes.index', compact('requisicoes', 'estatisticas'));
    }

    public function create(Livro $livro)
    {
        $user = Auth::user();

        if (!$user->podeRequisitar()) {
            return back()->with('erro', 'Já atingiu o limite de 3 livros requisitados em simultâneo.');
        }

        if (!$livro->isDisponivel()) {
            return back()->with('erro', 'Este livro não está disponível para requisição no momento.');
        }

        return view('requisicoes.create', compact('livro'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'livro_id' => 'required|exists:livros,id',
        ]);

        $user = Auth::user();
        $livro = Livro::find($request->livro_id);

        if (!$user->podeRequisitar()) {
            return back()->with('erro', 'Já atingiu o limite de 3 livros requisitados.');
        }

        if (!$livro->isDisponivel()) {
            return back()->with('erro', 'Livro não disponível.');
        }

        $requisicao = null;

        DB::transaction(function () use ($user, $livro, &$requisicao) {
            $ultimoCodigo = Requisicao::latest('id')->first();
            $numero = $ultimoCodigo ? intval(substr($ultimoCodigo->codigo, -4)) + 1 : 1;
            $codigo = 'REQ-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

            $dataRequisicao = now();
            $dataPrevista = $dataRequisicao->copy()->addDays(5);

            $requisicao = Requisicao::create([
                'codigo' => $codigo,
                'user_id' => $user->id,
                'livro_id' => $livro->id,
                'data_requisicao' => $dataRequisicao,
                'data_prevista_devolucao' => $dataPrevista,
                'status' => 'ativo',
            ]);
        });

        // ==================== ENVIO DE EMAILS ====================

        // Notificar o cidadão que fez a requisição
        try {
            $user->notify(new RequisicaoConfirmadaCidadao($requisicao));
        } catch (\Exception $e) {
            // Log do erro mas não interrompe o fluxo
            \Log::error('Erro ao enviar email para cidadão: ' . $e->getMessage());
        }

        // Notificar todos os administradores
        try {
            $admins = User::where('role', 'bibliotecario')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NovaRequisicaoAdmin($requisicao));
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao enviar email para admins: ' . $e->getMessage());
        }

        return redirect()->route('livros.index')
            ->with('sucesso', 'Requisição realizada com sucesso!');
    }

    public function show(Requisicao $requisicao)
    {
        // Se não for bibliotecário e a requisição não for do próprio, bloqueia
        if (!Auth::user()->isBibliotecario() && $requisicao->user_id !== Auth::id()) {
            abort(403, 'Não tens permissão para ver esta requisição.');
        }

        return view('requisicoes.show', compact('requisicao'));
    }

    public function confirmarDevolucao(Requisicao $requisicao)
    {
        if (!Auth::user()->isBibliotecario()) {
            abort(403);
        }

        if (!in_array($requisicao->status, ['ativo', 'devolucao_solicitada'])) {
            return back()->with('erro', 'Esta requisição não pode ser finalizada.');
        }

        DB::transaction(function () use ($requisicao) {
            $dataDevolucao = now();
            $diasAtraso = 0;

            if ($dataDevolucao->gt($requisicao->data_prevista_devolucao)) {
                $diasAtraso = $requisicao->data_prevista_devolucao->diffInDays($dataDevolucao);
            }

            $requisicao->update([
                'data_devolucao_real' => $dataDevolucao,
                'status' => 'concluido',
                'dias_atraso' => $diasAtraso,
            ]);
        });

        return redirect()->route('requisicoes.index')
            ->with('sucesso', 'Devolução confirmada com sucesso!');
    }

    public function historicoCidadao(User $user)
    {
        if (!Auth::user()->isBibliotecario()) {
            abort(403);
        }

        $requisicoes = $user->requisicoes()->with('livro')->latest()->paginate(10);

        return view('requisicoes.historico-cidadao', compact('user', 'requisicoes'));
    }

    public function minhasRequisicoes(Request $request)
    {
        $user = Auth::user();

        $query = Requisicao::with(['livro'])
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requisicoes = $query->latest()->paginate(10);

        $estatisticas = [
            'ativas' => $user->requisicoes()->whereIn('status', ['ativo'])->count(),
            'pendentes_devolucao' => $user->requisicoes()->where('status', 'devolucao_solicitada')->count(),
            'concluidas' => $user->requisicoes()->where('status', 'concluido')->count(),
        ];

        return view('requisicoes.minhas-requisicoes', compact('requisicoes', 'estatisticas'));
    }

    public function historicoLivro(Livro $livro)
    {
        $requisicoes = $livro->requisicoes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $estatisticas = [
            'total' => $livro->requisicoes->count(),
            'ativas' => $livro->requisicoes()->whereIn('status', ['ativo', 'devolucao_solicitada'])->count(),
            'concluidas' => $livro->requisicoes()->where('status', 'concluido')->count(),
            'atrasadas' => $livro->requisicoes()->where('status', 'atrasado')->count(),
        ];

        return view('requisicoes.historico-livro', compact('livro', 'requisicoes', 'estatisticas'));
    }

    public function solicitarDevolucao(Requisicao $requisicao)
    {
        if ($requisicao->user_id !== Auth::id()) {
            abort(403, 'Esta requisição não te pertence.');
        }

        if (!in_array($requisicao->status, ['ativo'])) {
            return back()->with('erro', 'Esta requisição não pode ser devolvida.');
        }

        $requisicao->update([
            'status' => 'devolucao_solicitada'
        ]);

        // ==================== NOTIFICAR ADMINS ====================
        try {
            $admins = User::where('role', 'bibliotecario')->get();
            foreach ($admins as $admin) {
                $admin->notify(new SolicitacaoDevolucaoAdmin($requisicao));
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao notificar admins sobre solicitação de devolução: ' . $e->getMessage());
        }

        return back()->with('sucesso', 'Solicitação de devolução enviada. Aguarda confirmação do bibliotecário.');


    }
}
