<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewAprovada extends Notification
{
    use Queueable;

    protected $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Review Aprovada - ' . $this->review->livro->nome)
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('A tua review foi aprovada e já está visível na página do livro.')
            ->line('---')
            ->line('**Detalhes da Review:**')
            ->line('**Livro:** ' . $this->review->livro->nome)
            ->line('**Classificação:** ' . $this->review->rating . ' ⭐')
            ->line('**Comentário:** ' . ($this->review->comentario ?? 'Sem comentário'))
            ->action('Ver Livro', route('livros.show', $this->review->livro))
            ->line('Obrigado pela tua contribuição! ');
    }
}
