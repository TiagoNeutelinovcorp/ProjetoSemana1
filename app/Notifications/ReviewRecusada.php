<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewRecusada extends Notification
{
    use Queueable;

    protected $review;
    protected $justificacao;

    public function __construct(Review $review, $justificacao)
    {
        $this->review = $review;
        $this->justificacao = $justificacao;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Review Recusada - ' . $this->review->livro->nome)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('A tua review foi analisada e infelizmente não foi aprovada.')
            ->line('---')
            ->line('**Detalhes da Review:**')
            ->line('**Livro:** ' . $this->review->livro->nome)
            ->line('**Classificação:** ' . $this->review->rating . ' ⭐')
            ->line('**Comentário:** ' . ($this->review->comentario ?? 'Sem comentário'))
            ->line('---')
            ->line('**Motivo da recusa:**')
            ->line($this->justificacao)
            ->line('---')
            ->line('Se tiveres dúvidas, contacta um administrador.');
    }
}
