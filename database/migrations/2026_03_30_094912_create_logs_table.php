<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('modulo');
            $table->unsignedBigInteger('objeto_id')->nullable();
            $table->string('acao');
            $table->text('alteracao')->nullable();
            $table->string('ip', 45);
            $table->text('browser');
            $table->timestamps();

            $table->index('modulo');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
