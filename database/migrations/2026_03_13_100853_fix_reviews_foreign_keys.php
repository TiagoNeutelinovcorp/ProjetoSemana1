<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite não suporta remoção direta de foreign keys, então vamos recriar a tabela
        Schema::dropIfExists('reviews');

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requisicao_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('livro_id');
            $table->tinyInteger('rating')->unsigned();
            $table->text('comentario')->nullable();
            $table->enum('status', ['suspenso', 'ativo', 'recusado'])->default('suspenso');
            $table->text('justificacao_recusa')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->unsignedBigInteger('aprovado_por')->nullable();
            $table->timestamps();

            // Índices sem foreign keys por enquanto
            $table->index('requisicao_id');
            $table->index('user_id');
            $table->index('livro_id');
            $table->unique(['user_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
