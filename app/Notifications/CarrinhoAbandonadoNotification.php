<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CarrinhoAbandonadoNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $itensCount;
    protected $total;

    public function __construct(User $user, $itensCount, $total)
    {
        $this->user = $user;
        $this->itensCount = $itensCount;
        $this->total = $total;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🛒 Carrinho Abandonado - Precisas de ajuda?')
            ->greeting('Olá ' . $this->user->name . '!')
            ->line('Reparámos que adicionaste **' . $this->itensCount . ' livro(s)** ao teu carrinho há mais de 1 hora e ainda não finalizaste a compra.')
            ->line('**Total do carrinho:** € ' . number_format($this->total, 2, ',', '.'))
            ->line('Precisas de ajuda com a tua encomenda?')
            ->action('Ver Carrinho', route('carrinho.index'))
            ->line('Se tiveres alguma dúvida ou problema, não hesites em contactar-nos.')
            ->line('A equipa da biblioteca está aqui para ajudar! 📚');
    }
}
