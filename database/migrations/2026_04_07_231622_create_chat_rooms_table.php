<?php
// database/migrations/xxxx_create_chat_rooms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['publico', 'privado'])->default('privado');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
