<?php

namespace App\Console\Commands;

use App\Models\Carrinho;
use App\Notifications\CarrinhoAbandonadoNotification;
use Illuminate\Console\Command;

class ProcessarCarrinhosAbandonados extends Command
{
    protected $signature = 'carrinhos:abandonados';
    protected $description = 'Envia notificações para carrinhos abandonados há mais de 1 hora';

    public function handle()
    {
        $this->info('🔍 A procurar carrinhos abandonados...');

        // Buscar carrinhos com mais de 1 hora
        $umaHoraAtras = now()->subHour();

        $carrinhos = Carrinho::with('user')
            ->where('adicionado_em', '<=', $umaHoraAtras)
            ->get()
            ->groupBy('user_id');

        $count = 0;

        foreach ($carrinhos as $userId => $itens) {
            $user = $itens->first()->user;
            $itensCount = $itens->count();

            $total = $itens->sum(function($item) {
                return $item->livro->preco * $item->quantidade;
            });

            try {
                $user->notify(new CarrinhoAbandonadoNotification($user, $itensCount, $total));
                $this->info("✅ Notificação enviada para: {$user->email} ({$itensCount} itens)");
                $count++;
            } catch (\Exception $e) {
                $this->error("❌ Erro ao notificar {$user->email}: " . $e->getMessage());
                \Log::error('Erro no carrinho abandonado: ' . $e->getMessage());
            }
        }

        $this->info("🎉 Processo concluído! {$count} notificações enviadas.");

        return Command::SUCCESS;
    }
}
