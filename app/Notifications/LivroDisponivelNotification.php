<?php

namespace App\Notifications;

use App\Models\Livro;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LivroDisponivelNotification extends Notification
{
    use Queueable;

    protected $livro;

    public function __construct(Livro $livro)
    {
        $this->livro = $livro;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Livro Disponível para Requisição')
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('O livro que pediste para ser notificado já está disponível!')
            ->line('---')
            ->line('**Livro:** ' . $this->livro->nome)
            ->line('**Autor(es):** ' . $this->livro->autores->pluck('nome')->implode(', '))
            ->line('**Editora:** ' . ($this->livro->editora->nome ?? 'N/A'))
            ->action('Requisitar Agora', route('requisicoes.create', $this->livro))
            ->line('Não percas tempo, faz já a tua requisição!');
    }
}
