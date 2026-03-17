<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NovaReviewAdmin extends Notification
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
            ->subject('Nova Review Submetida - Aguarda Aprovação')
            ->greeting('Olá Administrador!')
            ->line('Uma nova review foi submetida e aguarda a tua aprovação.')
            ->line('---')
            ->line('**Detalhes da Review:**')
            ->line('**Cidadão:** ' . $this->review->user->name)
            ->line('**Email:** ' . $this->review->user->email)
            ->line('**Livro:** ' . $this->review->livro->nome)
            ->line('**Classificação:** ' . $this->review->rating . ' ⭐')
            ->line('**Comentário:** ' . ($this->review->comentario ?? 'Sem comentário'))
            ->action('Ver Review', route('admin.reviews.show', $this->review))
            ->line('Acede à área de administração para aprovar ou recusar esta review.');
    }
}
