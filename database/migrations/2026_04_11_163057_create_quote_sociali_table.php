<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quote_sociali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')
                ->constrained('soci')
                ->cascadeOnDelete();
            $table->integer('anno');
            $table->decimal('importo', 8, 2);
            $table->date('data_pagamento');
            $table->enum('conto', ['cassa', 'banca']);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['socio_id', 'anno']); // un socio paga una sola quota per anno
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_sociali');
    }
};
