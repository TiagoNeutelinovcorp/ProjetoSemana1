<?php

namespace App\Notifications;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RequisicaoConfirmadaCidadao extends Notification
{
    use Queueable;

    protected $requisicao;

    public function __construct(Requisicao $requisicao)
    {
        $this->requisicao = $requisicao;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Requisição Confirmada - ' . $this->requisicao->codigo)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('A tua requisição foi confirmada com sucesso.')
            ->line('---')
            ->line('**Detalhes da Requisição:**')
            ->line('**Livro:** ' . $this->requisicao->livro->nome)
            ->line('**Data da Requisição:** ' . $this->requisicao->data_requisicao->format('d/m/Y H:i'))
            ->line('**Data Prevista de Devolução:** ' . $this->requisicao->data_prevista_devolucao->format('d/m/Y'))
            ->line('**Código da Requisição:** ' . $this->requisicao->codigo)
            ->action('Ver Detalhes', route('requisicoes.show', $this->requisicao))
            ->line('Obrigado por utilizares a nossa biblioteca!');
    }
}
