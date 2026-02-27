<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Para SQLite, precisamos recriar a tabela com a nova coluna
        Schema::create('users_new', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['cliente', 'bibliotecario', 'admin'])->default('cliente');
            $table->timestamps();
        });

        // Copiar dados da tabela antiga
        DB::table('users_new')->insert(
            DB::table('users')->get()->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => 'cliente',
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ])->toArray()
        );

        // Remover tabela antiga e renomear nova
        Schema::dropIfExists('users');
        Schema::rename('users_new', 'users');
    }

    public function down(): void
    {
        // Reverter para tabela sem role
        Schema::create('users_old', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        DB::table('users_old')->insert(
            DB::table('users')->get()->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ])->toArray()
        );

        Schema::dropIfExists('users');
        Schema::rename('users_old', 'users');
    }
};
