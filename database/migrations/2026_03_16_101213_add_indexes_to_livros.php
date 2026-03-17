<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Índices para melhor performance nas buscas
        Schema::table('livros', function (Blueprint $table) {
            $table->index('bibliografia');
            $table->index('nome');
        });
    }

    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropIndex(['bibliografia']);
            $table->dropIndex(['nome']);
        });
    }
};
