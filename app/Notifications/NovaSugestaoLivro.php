<?php

namespace App\Notifications;

use App\Models\LivroSugestao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NovaSugestaoLivro extends Notification
{
    use Queueable;

    protected $sugestao;

    public function __construct(LivroSugestao $sugestao)
    {
        $this->sugestao = $sugestao;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nova Sugestão de Livro - ' . $this->sugestao->titulo)
            ->greeting('Olá Administrador!')
            ->line('Um novo livro foi sugerido por um utilizador.')
            ->line('---')
            ->line('**Detalhes da Sugestão:**')
            ->line('**Título:** ' . $this->sugestao->titulo)
            ->line('**Autor(es):** ' . $this->sugestao->autores)
            ->line('**Editora:** ' . $this->sugestao->editora)
            ->line('**ISBN:** ' . ($this->sugestao->isbn ?? 'N/A'))
            ->line('**Sugerido por:** ' . $this->sugestao->user->name)
            ->line('**Email do utilizador:** ' . $this->sugestao->user->email)
            ->action('Ver Sugestões', route('google-books.sugestoes'))
            ->line('Acede à área de administração para aprovar ou rejeitar esta sugestão.');
    }
}
