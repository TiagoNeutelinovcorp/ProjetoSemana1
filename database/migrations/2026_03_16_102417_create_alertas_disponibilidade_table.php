<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_disponibilidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('livro_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pendente', 'enviado', 'cancelado'])->default('pendente');
            $table->timestamp('notificado_em')->nullable();
            $table->timestamps();

            // Garantir que um utilizador só pode ter um alerta pendente por livro
            $table->unique(['user_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_disponibilidade');
    }
};
