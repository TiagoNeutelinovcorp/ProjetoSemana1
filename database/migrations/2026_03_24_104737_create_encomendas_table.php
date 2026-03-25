<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('numero_encomenda')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('pendente_pagamento');
            $table->string('metodo_pagamento')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('stripe_payment_method_id')->nullable();
            $table->string('morada');
            $table->string('cidade');
            $table->string('codigo_postal');
            $table->string('pais')->default('Portugal');
            $table->string('telefone');
            $table->timestamp('pago_em')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('entregue_em')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('numero_encomenda');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
