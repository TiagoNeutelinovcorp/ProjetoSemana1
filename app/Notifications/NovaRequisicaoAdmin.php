<?php

namespace App\Notifications;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NovaRequisicaoAdmin extends Notification
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
            ->subject('Nova Requisição - ' . $this->requisicao->codigo)
            ->greeting('Olá Administrador!')
            ->line('Uma nova requisição foi realizada na biblioteca.')
            ->line('---')
            ->line('**Detalhes da Requisição:**')
            ->line('**Cidadão:** ' . $this->requisicao->user->name)
            ->line('**Email:** ' . $this->requisicao->user->email)
            ->line('**Livro:** ' . $this->requisicao->livro->nome)
            ->line('**Data da Requisição:** ' . $this->requisicao->data_requisicao->format('d/m/Y H:i'))
            ->line('**Data Prevista de Devolução:** ' . $this->requisicao->data_prevista_devolucao->format('d/m/Y'))
            ->line('**Código:** ' . $this->requisicao->codigo)
            ->action('Ver Requisição', route('requisicoes.show', $this->requisicao))
            ->line('Para confirmar a devolução, acede à área de administração.');
    }
}
