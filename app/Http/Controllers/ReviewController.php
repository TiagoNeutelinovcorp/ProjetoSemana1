<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NovaReviewAdmin;
use App\Notifications\ReviewAprovada;
use App\Notifications\ReviewRecusada;

class ReviewController extends Controller
{
    /**
     * Mostrar formulário de review para uma requisição
     */
    public function create(Requisicao $requisicao)
    {
        // Verificar se a requisição pertence ao utilizador
        if ($requisicao->user_id !== Auth::id()) {
            abort(403, 'Esta requisição não te pertence.');
        }

        // Verificar se a requisição já foi concluída
        if ($requisicao->status !== 'concluido') {
            \Log::info('Review bloqueada: requisição não concluída');
            return redirect()->route('requisicoes.show', $requisicao)
                ->with('erro', 'Só podes avaliar livros após a devolução ser confirmada.');
        }

        // Verificar se já existe review para esta requisição
        if ($requisicao->review) {
            \Log::info('Review bloqueada: já existe review para esta requisição');
            return redirect()->route('requisicoes.show', $requisicao)
                ->with('erro', 'Já submeteste uma review para esta requisição.');
        }

        // VERIFICAR SE JÁ EXISTE REVIEW PARA ESTE LIVRO
        $reviewExistente = Review::where('user_id', Auth::id())
            ->where('livro_id', $requisicao->livro_id)
            ->first();

        if ($reviewExistente) {
            \Log::info('Review bloqueada: já existe review para este livro', [
                'user_id' => Auth::id(),
                'livro_id' => $requisicao->livro_id,
                'review_id' => $reviewExistente->id
            ]);

            return redirect()->route('requisicoes.show', $requisicao)
                ->with('erro', 'Já submeteste uma review para este livro. Cada livro só pode ser avaliado uma vez.');
        }

        return view('reviews.create', compact('requisicao'));
    }

    /**
     * Guardar nova review
     */
    public function store(Request $request, Requisicao $requisicao)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        // Verificar permissões
        if ($requisicao->user_id !== Auth::id()) {
            abort(403, 'Esta requisição não te pertence.');
        }

        if ($requisicao->status !== 'concluido') {
            return redirect()->route('requisicoes.show', $requisicao)
                ->with('erro', 'Só podes avaliar livros após a devolução ser confirmada.');
        }

        // Verificar se já existe review para esta requisição
        if ($requisicao->review) {
            return redirect()->route('requisicoes.show', $requisicao)
                ->with('erro', 'Já submeteste uma review para esta requisição.');
        }

        // VERIFICAÇÃO CRÍTICA: Já existe review para este livro?
        $reviewExistente = Review::where('user_id', Auth::id())
            ->where('livro_id', $requisicao->livro_id)
            ->first();

        if ($reviewExistente) {
            return redirect()->route('requisicoes.show', $requisicao)
                ->with('erro', 'Já submeteste uma review para este livro. Cada livro só pode ser avaliado uma vez.');
        }

        // Criar review
        $review = Review::create([
            'requisicao_id' => $requisicao->id,
            'user_id' => Auth::id(),
            'livro_id' => $requisicao->livro_id,
            'rating' => $request->rating,
            'comentario' => $request->comentario,
            'status' => 'suspenso',
        ]);

        // Notificar todos os administradores
        try {
            $admins = User::where('role', 'bibliotecario')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NovaReviewAdmin($review));
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao notificar admins sobre nova review: ' . $e->getMessage());
        }

        return redirect()->route('requisicoes.show', $requisicao)
            ->with('sucesso', 'Review submetida com sucesso! Aguarda aprovação de um administrador.');
    }

    /**
     * Listar reviews para admin (pendentes e todas)
     */
    public function adminIndex(Request $request)
    {
        if (!Auth::user()->isBibliotecario()) {
            abort(403);
        }

        $query = Review::with(['user', 'livro', 'requisicao']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('livro')) {
            $query->whereHas('livro', function($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->livro . '%');
            });
        }

        $reviews = $query->latest()->paginate(15);

        $estatisticas = [
            'pendentes' => Review::where('status', 'suspenso')->count(),
            'ativos' => Review::where('status', 'ativo')->count(),
            'recusados' => Review::where('status', 'recusado')->count(),
        ];

        return view('reviews.admin-index', compact('reviews', 'estatisticas'));
    }

    /**
     * Mostrar detalhe da review (admin)
     */
    public function adminShow(Review $review)
    {
        if (!Auth::user()->isBibliotecario()) {
            abort(403);
        }

        $review->load(['user', 'livro', 'requisicao', 'aprovador']);

        return view('reviews.admin-show', compact('review'));
    }

    /**
     * Aprovar review (admin)
     */
    public function aprovar(Review $review)
    {
        if (!Auth::user()->isBibliotecario()) {
            abort(403);
        }

        if ($review->status !== 'suspenso') {
            return back()->with('erro', 'Esta review já foi processada.');
        }

        $review->update([
            'status' => 'ativo',
            'aprovado_em' => now(),
            'aprovado_por' => Auth::id(),
        ]);

        // Notificar o cidadão
        try {
            $review->user->notify(new ReviewAprovada($review));
        } catch (\Exception $e) {
            \Log::error('Erro ao notificar utilizador sobre review aprovada: ' . $e->getMessage());
        }

        return redirect()->route('admin.reviews.index')
            ->with('sucesso', 'Review aprovada com sucesso! O utilizador foi notificado.');
    }

    /**
     * Recusar review (admin)
     */
    public function recusar(Request $request, Review $review)
    {
        if (!Auth::user()->isBibliotecario()) {
            abort(403);
        }

        $request->validate([
            'justificacao' => 'required|string|max:500',
        ]);

        if ($review->status !== 'suspenso') {
            return back()->with('erro', 'Esta review já foi processada.');
        }

        $review->update([
            'status' => 'recusado',
            'justificacao_recusa' => $request->justificacao,
            'aprovado_em' => now(),
            'aprovado_por' => Auth::id(),
        ]);

        // Notificar o cidadão
        try {
            $review->user->notify(new ReviewRecusada($review, $request->justificacao));
        } catch (\Exception $e) {
            \Log::error('Erro ao notificar utilizador sobre review recusada: ' . $e->getMessage());
        }

        return redirect()->route('admin.reviews.index')
            ->with('sucesso', 'Review recusada com sucesso. O utilizador foi notificado.');
    }

    /**
     * API para rating médio (usado em AJAX)
     */
    public function livroRating($livroId)
    {
        $livro = \App\Models\Livro::find($livroId);
        return response()->json([
            'media' => $livro->rating_medio,
            'total' => $livro->total_reviews,
        ]);
    }

}
