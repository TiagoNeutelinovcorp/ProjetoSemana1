<?php

namespace App\Jobs\StripeWebhooks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\Carrinho;
use Illuminate\Support\Facades\Log;

class PaymentIntentSucceededJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        Log::info('PaymentIntentSucceededJob executado!');

        $payload = $this->webhookCall->payload;
        $paymentIntent = $payload['data']['object'];
        $paymentIntentId = $paymentIntent['id'];

        Log::info('PaymentIntent ID: ' . $paymentIntentId);

        // Procura a encomenda por payment_intent_id
        $encomenda = Encomenda::where('stripe_payment_intent_id', $paymentIntentId)->first();

        if ($encomenda) {
            Log::info('Encomenda encontrada, status: ' . $encomenda->status);

            if ($encomenda->status === 'pendente_pagamento') {
                $encomenda->update([
                    'status' => 'pago',
                    'pago_em' => now(),
                ]);
                Log::info('Encomenda atualizada para PAGO');
            }
        } else {
            Log::warning('Encomenda não encontrada para PaymentIntent: ' . $paymentIntentId);
        }
    }
}
