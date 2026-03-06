<?php

namespace App\Notifications;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LembreteDevolucao extends Notification
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
            ->subject('Lembrete: Devolução Amanhã - ' . $this->requisicao->codigo)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('Este é um lembrete que o livro requisitado deve ser devolvido **amanhã**.')
            ->line('---')
            ->line('**Detalhes da Requisição:**')
            ->line('**Livro:** ' . $this->requisicao->livro->nome)
            ->line('**Data da Requisição:** ' . $this->requisicao->data_requisicao->format('d/m/Y'))
            ->line('**Data de Devolução Prevista:** ' . $this->requisicao->data_prevista_devolucao->format('d/m/Y'))
            ->line('**Código:** ' . $this->requisicao->codigo)
            ->action('Ver Detalhes', route('requisicoes.show', $this->requisicao))
            ->line('Por favor, certifica-te de entregar o livro na biblioteca dentro do prazo.')
            ->line('Obrigado!');
    }
}
