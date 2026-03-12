<?php

namespace App\Notifications;

use App\Models\LivroSugestao;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SugestaoRejeitada extends Notification
{
    use Queueable;

    protected $sugestao;
    protected $motivo;

    public function __construct(LivroSugestao $sugestao, $motivo)
    {
        $this->sugestao = $sugestao;
        $this->motivo = $motivo;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Sugestão de Livro Rejeitada')
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('A tua sugestão de livro foi analisada e infelizmente não foi aprovada.')
            ->line('---')
            ->line('**Detalhes da Sugestão:**')
            ->line('**Título:** ' . $this->sugestao->titulo)
            ->line('**Autor(es):** ' . $this->sugestao->autores)
            ->line('**Editora:** ' . $this->sugestao->editora)
            ->line('**ISBN:** ' . ($this->sugestao->isbn ?? 'N/A'))
            ->line('---')
            ->line('**Motivo da rejeição:**')
            ->line($this->motivo)
            ->line('---')
            ->line('Se tiveres dúvidas, por favor contacta um administrador da biblioteca.')
            ->line('Obrigado pela tua compreensão e continua a contribuir com sugestões! 📚');
    }
}
