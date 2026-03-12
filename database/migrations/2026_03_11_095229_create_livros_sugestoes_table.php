<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livros_sugestoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->string('autores');
            $table->string('editora');
            $table->text('descricao')->nullable();
            $table->string('isbn')->nullable();
            $table->string('capa_thumbnail')->nullable();
            $table->string('capa_grande')->nullable();
            $table->integer('paginas')->nullable();
            $table->string('data_publicacao')->nullable();
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->text('observacoes_admin')->nullable();
            $table->timestamp('aprovado_em')->nullable();
            $table->foreignId('aprovado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros_sugestoes');
    }
};
