<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fatture', function (Blueprint $table) {
            $table->enum('stato', ['aperta', 'parziale', 'pagata'])->default('aperta')->change();
        });
    }

    public function down(): void
    {
        Schema::table('fatture', function (Blueprint $table) {
            $table->enum('stato', ['aperta', 'pagata'])->default('aperta')->change();
        });
    }
};