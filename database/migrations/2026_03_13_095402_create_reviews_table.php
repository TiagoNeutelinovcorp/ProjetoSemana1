<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisicao_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // 1 a 5 estrelas
            $table->text('comentario')->nullable();
            $table->enum('status', ['suspenso', 'ativo', 'recusado'])->default('suspenso');
            $table->text('justificacao_recusa')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->foreignId('aprovado_por')->nullable()->constrained('users');
            $table->timestamps();

            // Garantir que um utilizador só pode fazer um review por livro
            $table->unique(['user_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
