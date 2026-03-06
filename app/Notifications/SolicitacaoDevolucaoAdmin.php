<?php

namespace App\Notifications;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SolicitacaoDevolucaoAdmin extends Notification
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
            ->subject('Solicitação de Devolução - ' . $this->requisicao->codigo)
            ->greeting('Olá Administrador!')
            ->line('O cidadão **' . $this->requisicao->user->name . '** solicitou a devolução de um livro.')
            ->line('---')
            ->line('**Detalhes da Solicitação:**')
            ->line('**Cidadão:** ' . $this->requisicao->user->name)
            ->line('**Email:** ' . $this->requisicao->user->email)
            ->line('**Livro:** ' . $this->requisicao->livro->nome)
            ->line('**Data da Requisição:** ' . $this->requisicao->data_requisicao->format('d/m/Y'))
            ->line('**Data Prevista de Devolução:** ' . $this->requisicao->data_prevista_devolucao->format('d/m/Y'))
            ->line('**Código da Requisição:** ' . $this->requisicao->codigo)
            ->line('**Status atual:** Aguardando confirmação de devolução')
            ->action('Confirmar Devolução', route('requisicoes.index'))
            ->line('Acede à área de administração para processar a devolução.');
    }
}
