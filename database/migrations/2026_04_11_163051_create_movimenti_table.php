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
        Schema::create('movimenti', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->string('descrizione');
            $table->enum('tipo', ['entrata', 'uscita']);
            $table->enum('conto', ['cassa', 'banca']);
            $table->decimal('importo', 10, 2);
            $table->foreignId('categoria_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();
            $table->string('allegato')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimenti');
    }
};
