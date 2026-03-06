<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite não suporta alteração direta de ENUM, então vamos recriar a tabela
        // Primeiro, criar tabela temporária com os dados
        DB::statement('CREATE TABLE requisicoes_temp AS SELECT * FROM requisicoes');

        // Dropar a tabela original
        Schema::drop('requisicoes');

        // Recriar a tabela com o novo ENUM
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->datetime('data_requisicao');
            $table->datetime('data_prevista_devolucao');
            $table->datetime('data_devolucao_real')->nullable();
            // Adicionar 'devolucao_solicitada' ao enum
            $table->enum('status', ['pendente', 'ativo', 'devolucao_solicitada', 'concluido', 'atrasado'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->integer('dias_atraso')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('data_prevista_devolucao');
        });

        // Copiar os dados de volta (status antigos são mantidos)
        DB::statement('INSERT INTO requisicoes (id, codigo, user_id, livro_id, data_requisicao, data_prevista_devolucao, data_devolucao_real, status, observacoes, dias_atraso, created_at, updated_at)
                       SELECT id, codigo, user_id, livro_id, data_requisicao, data_prevista_devolucao, data_devolucao_real, status, observacoes, dias_atraso, created_at, updated_at
                       FROM requisicoes_temp');

        // Dropar a tabela temporária
        Schema::drop('requisicoes_temp');
    }

    public function down(): void
    {
        // Reverter para o enum anterior (remover 'devolucao_solicitada')
        DB::statement('CREATE TABLE requisicoes_temp AS SELECT * FROM requisicoes');

        Schema::drop('requisicoes');

        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->datetime('data_requisicao');
            $table->datetime('data_prevista_devolucao');
            $table->datetime('data_devolucao_real')->nullable();
            // Voltar ao enum original
            $table->enum('status', ['pendente', 'ativo', 'concluido', 'atrasado'])->default('ativo');
            $table->text('observacoes')->nullable();
            $table->integer('dias_atraso')->default(0);
            $table->timestamps();

            $table->index('status');
            $table->index('data_prevista_devolucao');
        });

        DB::statement('INSERT INTO requisicoes SELECT * FROM requisicoes_temp');
        Schema::drop('requisicoes_temp');
    }
};
