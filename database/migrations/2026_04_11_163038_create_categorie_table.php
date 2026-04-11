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
        Schema::create('categorie', function (Blueprint $table) {
            $table->id();
            $table->string('nome');              // es. "Spese operative", "Quote soci"
            $table->enum('tipo', ['entrata', 'uscita', 'entrambi']); // a quale tipo di movimento si applica
            $table->string('colore')->default('#6366f1'); // colore per l'interfaccia (hex)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorie');
    }
};
