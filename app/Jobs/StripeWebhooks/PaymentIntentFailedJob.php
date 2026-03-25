<?php

namespace App\Jobs\StripeWebhooks;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;
use App\Models\Encomenda;
use App\Models\EncomendaHistorico;

class PaymentIntentFailedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WebhookCall $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        $payload = $this->webhookCall->payload;
        $paymentIntent = $payload['data']['object'];

        $encomenda = Encomenda::where('stripe_payment_intent_id', $paymentIntent['id'])->first();

        if ($encomenda) {
            EncomendaHistorico::create([
                'encomenda_id' => $encomenda->id,
                'estado_novo' => $encomenda->status,
                'observacoes' => 'Pagamento falhou: ' . ($paymentIntent['last_payment_error']['message'] ?? 'Erro desconhecido'),
            ]);
        }
    }
}
