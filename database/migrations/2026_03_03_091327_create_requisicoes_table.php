<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Numeração sequencial tipo REQ-2025-0001
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Cidadão
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->datetime('data_requisicao');
            $table->datetime('data_prevista_devolucao'); // data_requisicao + 5 dias
            $table->datetime('data_devolucao_real')->nullable(); // Quando o admin confirma a devolução
            $table->enum('status', ['pendente', 'ativo', 'concluido', 'atrasado'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->integer('dias_atraso')->default(0);
            $table->timestamps();

            // Índices para otimizar consultas
            $table->index('status');
            $table->index('data_prevista_devolucao');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};
