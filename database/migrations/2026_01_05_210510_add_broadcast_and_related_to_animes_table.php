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
        Schema::table('animes', function (Blueprint $table) {
            $table->json('broadcast')->nullable()->after('source'); // Para o calendário {day, time}
            $table->json('related_animes')->nullable()->after('broadcast'); // Para listar temporadas
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn(['broadcast', 'related_animes']);
        });
    }
};
