<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autor_livro', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autor_id')->constrained('autores')->onDelete('cascade');
            $table->foreignId('livro_id')->constrained('livros')->onDelete('cascade');
            $table->timestamps();

            // Evitar duplicados
            $table->unique(['autor_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autor_livro');
    }
};
