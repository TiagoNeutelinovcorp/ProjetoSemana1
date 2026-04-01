<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->integer('stock')->default(0)->after('preco');
            $table->foreignId('autor_id')->nullable()->constrained('autores')->onDelete('set null')->after('editora_id');
        });
    }

    public function down(): void
    {
        Schema::table('livros', function (Blueprint $table) {
            $table->dropColumn('stock');
            $table->dropForeign(['autor_id']);
            $table->dropColumn('autor_id');
        });
    }
};
