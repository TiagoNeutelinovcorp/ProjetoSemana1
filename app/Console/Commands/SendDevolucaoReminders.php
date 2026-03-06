<?php

namespace App\Console\Commands;

use App\Models\Requisicao;
use App\Notifications\LembreteDevolucao;
use Illuminate\Console\Command;

class SendDevolucaoReminders extends Command
{
    protected $signature = 'reminders:devolucao';
    protected $description = 'Envia lembretes para cidadãos sobre devoluções no dia seguinte';

    public function handle()
    {
        $this->info('🔍 A procurar requisições com devolução amanhã...');

        // Data de amanhã (início do dia para comparação)
        $amanha = now()->addDay()->startOfDay();

        $requisicoes = Requisicao::with(['user', 'livro'])
            ->whereDate('data_prevista_devolucao', $amanha)
            ->whereIn('status', ['ativo'])
            ->get();

        $count = 0;

        foreach ($requisicoes as $requisicao) {
            try {
                $requisicao->user->notify(new LembreteDevolucao($requisicao));
                $this->info("Lembrete enviado para: {$requisicao->user->email} - Livro: {$requisicao->livro->nome}");
                $count++;
            } catch (\Exception $e) {
                $this->error("Erro ao enviar para {$requisicao->user->email}: " . $e->getMessage());
                \Log::error('Erro no lembrete de devolução: ' . $e->getMessage());
            }
        }

        $this->info("Processo concluído! {$count} lembretes enviados com sucesso.");

        return Command::SUCCESS;
    }
}
