<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livros_sugestoes', function (Blueprint $table) {
            $table->decimal('preco', 8, 2)->default(0)->after('data_publicacao');
        });
    }

    public function down(): void
    {
        Schema::table('livros_sugestoes', function (Blueprint $table) {
            $table->dropColumn('preco');
        });
    }
};
