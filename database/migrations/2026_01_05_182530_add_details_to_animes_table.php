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
            $table->date('start_date')->nullable()->after('num_episodes');
            $table->json('studios')->nullable()->after('start_date');
            $table->string('source')->nullable()->after('studios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'studios', 'source']);
        });
    }
};
