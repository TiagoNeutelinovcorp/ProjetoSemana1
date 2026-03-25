<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\Livro;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\EncomendaHistorico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;

class EncomendaController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }

    /**
     * Adicionar livro ao carrinho
     */
    public function adicionarAoCarrinho(Livro $livro)
    {
        if (!auth()->user()->two_factor_secret) {
            return redirect()->route('profile.index')
                ->with('erro', 'Precisas de ativar o 2FA para fazer compras.');
        }

        if (!$livro->isDisponivel()) {
            return redirect()->route('livros.show', $livro)
                ->with('erro', 'Este livro não está disponível para compra.');
        }

        $carrinho = Carrinho::where('user_id', Auth::id())
            ->where('livro_id', $livro->id)
            ->first();

        if ($carrinho) {
            $carrinho->increment('quantidade');
        } else {
            Carrinho::create([
                'user_id' => Auth::id(),
                'livro_id' => $livro->id,
                'quantidade' => 1,
                'adicionado_em' => now(),
            ]);
        }

        return redirect()->route('carrinho.index')
            ->with('sucesso', 'Livro adicionado ao carrinho!');
    }

    /**
     * Ver carrinho
     */
    public function verCarrinho()
    {
        $itens = Carrinho::with('livro')
            ->where('user_id', Auth::id())
            ->get();

        $total = $itens->sum(function($item) {
            return $item->livro->preco * $item->quantidade;
        });

        $iva = $total * 0.23;
        $totalComIva = $total + $iva;

        return view('carrinho.index', compact('itens', 'total', 'iva', 'totalComIva'));
    }

    /**
     * Atualizar quantidade no carrinho
     */
    public function atualizarCarrinho(Request $request, Carrinho $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantidade' => 'required|integer|min:1|max:10',
        ]);

        $item->update([
            'quantidade' => $request->quantidade,
        ]);

        return redirect()->route('carrinho.index')
            ->with('sucesso', 'Carrinho atualizado!');
    }

    /**
     * Remover item do carrinho
     */
    public function removerDoCarrinho(Carrinho $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403);
        }

        $item->delete();

        return redirect()->route('carrinho.index')
            ->with('sucesso', 'Item removido do carrinho!');
    }

    /**
     * Checkout - passo 1: mostrar formulário de morada
     */
    public function checkoutMorada()
    {
        $itens = Carrinho::with('livro')
            ->where('user_id', Auth::id())
            ->get();

        if ($itens->isEmpty()) {
            return redirect()->route('carrinho.index')
                ->with('erro', 'O teu carrinho está vazio.');
        }

        $total = $itens->sum(function($item) {
            return $item->livro->preco * $item->quantidade;
        });

        $iva = $total * 0.23;
        $totalComIva = $total + $iva;

        return view('carrinho.checkout-morada', compact('itens', 'total', 'iva', 'totalComIva'));
    }

    /**
     * Checkout - passo 2: processar morada e ir para pagamento
     */
    public function checkoutProcessarMorada(Request $request)
    {
        $request->validate([
            'morada' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:20',
            'telefone' => 'required|string|max:20',
        ]);

        session([
            'checkout_morada' => $request->only(['morada', 'cidade', 'codigo_postal', 'telefone'])
        ]);

        return redirect()->route('checkout.pagamento');
    }

    /**
     * Checkout - passo 3: mostrar página de pagamento (Stripe)
     */
    public function checkoutPagamento()
    {
        $itens = Carrinho::with('livro')
            ->where('user_id', Auth::id())
            ->get();

        if ($itens->isEmpty()) {
            return redirect()->route('carrinho.index')
                ->with('erro', 'O teu carrinho está vazio.');
        }

        $morada = session('checkout_morada');
        if (!$morada) {
            return redirect()->route('checkout.morada');
        }

        $total = $itens->sum(function($item) {
            return $item->livro->preco * $item->quantidade;
        });

        $iva = $total * 0.23;
        $totalComIva = $total + $iva;

        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => (int) round($totalComIva * 100),
            'currency' => 'eur',
            'metadata' => [
                'user_id' => Auth::id(),
            ],
        ]);

        return view('carrinho.checkout-pagamento', compact(
            'itens', 'total', 'iva', 'totalComIva', 'morada', 'paymentIntent'
        ));
    }

    /**
     * Confirmar pagamento e criar encomenda
     */
    public function checkoutConfirmar(Request $request)
{
    // ADICIONA ISTO
    \Log::info('=== CHECKOUT CONFIRMAR FOI CHAMADO ===');

    $request->validate([
        'payment_intent_id' => 'required|string',
        'payment_method_id' => 'required|string',
    ]);

    $itens = Carrinho::with('livro')
        ->where('user_id', Auth::id())
        ->get();

    if ($itens->isEmpty()) {
        return redirect()->route('carrinho.index')
            ->with('erro', 'O teu carrinho está vazio.');
    }

    $morada = session('checkout_morada');
    if (!$morada) {
        return redirect()->route('checkout.morada');
    }

    $total = $itens->sum(function($item) {
        return $item->livro->preco * $item->quantidade;
    });

    $totalComIva = $total * 1.23;

    // Criar encomenda DIRETAMENTE, sem transação
    $encomenda = Encomenda::create([
        'user_id' => Auth::id(),
        'numero_encomenda' => 'ENC-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
        'subtotal' => $total,
        'iva' => $total * 0.23,
        'total' => $totalComIva,
        'status' => 'pago',
        'metodo_pagamento' => 'stripe',
        'stripe_payment_intent_id' => $request->payment_intent_id,
        'stripe_payment_method_id' => $request->payment_method_id,
        'morada' => $morada['morada'],
        'cidade' => $morada['cidade'],
        'codigo_postal' => $morada['codigo_postal'],
        'telefone' => $morada['telefone'],
        'pago_em' => now(),
    ]);

    // Adicionar itens
    foreach ($itens as $item) {
        EncomendaItem::create([
            'encomenda_id' => $encomenda->id,
            'livro_id' => $item->livro_id,
            'quantidade' => $item->quantidade,
            'preco_unitario' => $item->livro->preco,
            'subtotal' => $item->livro->preco * $item->quantidade,
        ]);
    }

    // Limpar carrinho
    Carrinho::where('user_id', Auth::id())->delete();
    session()->forget('checkout_morada');

    // Guardar na sessão
    session([
        'numero_encomenda' => $encomenda->numero_encomenda,
        'total' => $encomenda->total
    ]);

    return redirect()->route('encomendas.sucesso')
        ->with('sucesso', 'Encomenda realizada com sucesso!');
}

    /**
     * Página de sucesso da encomenda
     */
    public function sucesso()
    {
        $numero_encomenda = session('numero_encomenda');
        $total = session('total');

        return view('encomendas.sucesso', compact('numero_encomenda', 'total'));
    }

    /**
     * Listar encomendas do utilizador
     */
    public function minhasEncomendas()
    {
        $encomendas = Encomenda::with('itens.livro')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('encomendas.minhas', compact('encomendas'));
    }

    /**
     * Ver detalhe da encomenda
     */
    public function show(Encomenda $encomenda)
    {
        if (!Auth::check()) {
            abort(403);
        }

        if ($encomenda->user_id !== Auth::id() && !Auth::user()->isBibliotecario()) {
            abort(403, 'Não tens permissão para ver esta encomenda.');
        }

        $encomenda->load(['itens.livro', 'historico.user']);

        return view('encomendas.show', compact('encomenda'));
    }

    /**
     * Admin: Listar todas as encomendas
     */
    public function adminIndex(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isBibliotecario()) {
            abort(403, 'Acesso negado. Apenas bibliotecários podem ver esta página.');
        }

        $query = Encomenda::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_encomenda', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $encomendas = $query->orderBy('created_at', 'desc')->paginate(15);

        $estatisticas = [
            'pendentes' => Encomenda::where('status', 'pendente_pagamento')->count(),
            'pagas_hoje' => Encomenda::whereDate('pago_em', today())->count(),
            'total_mes' => Encomenda::whereMonth('created_at', now()->month)->sum('total'),
        ];

        return view('encomendas.admin-index', compact('encomendas', 'estatisticas'));
    }

    /**
     * Admin: Atualizar estado da encomenda
     */
    public function adminUpdateStatus(Request $request, Encomenda $encomenda)
    {
        if (!Auth::check() || !Auth::user()->isBibliotecario()) {
            abort(403, 'Acesso negado. Apenas bibliotecários podem atualizar encomendas.');
        }

        $request->validate([
            'status' => 'required|in:pendente_pagamento,pago,processando,enviado,entregue,cancelado',
            'observacoes' => 'nullable|string|max:255',
        ]);

        $estadoAnterior = $encomenda->status;

        $encomenda->update([
            'status' => $request->status,
            'enviado_em' => $request->status === 'enviado' ? now() : $encomenda->enviado_em,
            'entregue_em' => $request->status === 'entregue' ? now() : $encomenda->entregue_em,
        ]);

        EncomendaHistorico::create([
            'encomenda_id' => $encomenda->id,
            'user_id' => Auth::id(),
            'estado_anterior' => $estadoAnterior,
            'estado_novo' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return back()->with('sucesso', 'Estado da encomenda atualizado!');
    }
}
