<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique(); // ISBN único
            $table->string('nome');
            $table->text('bibliografia')->nullable();
            $table->string('imagem_capa')->nullable(); // caminho da imagem
            $table->decimal('preco', 8, 2)->default(0);

            // Chave estrangeira para editora
            $table->foreignId('editora_id')->constrained('editoras')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
