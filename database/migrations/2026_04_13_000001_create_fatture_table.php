<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fatture', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->nullable();
            $table->date('data');
            $table->date('data_scadenza')->nullable();
            $table->enum('tipo', ['attiva', 'passiva']);
            $table->string('controparte');
            $table->decimal('importo', 10, 2);
            $table->string('descrizione');
            $table->enum('stato', ['aperta', 'pagata'])->default('aperta');
            $table->foreignId('categoria_id')->nullable()->constrained('categorie')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->timestamps();
        });

        Schema::table('movimenti', function (Blueprint $table) {
            $table->foreignId('fattura_id')->nullable()->constrained('fatture')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('movimenti', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Fattura::class);
        });
        Schema::dropIfExists('fatture');
    }
};