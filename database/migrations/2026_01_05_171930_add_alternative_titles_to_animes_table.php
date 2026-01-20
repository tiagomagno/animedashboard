<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->json('alternative_titles')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn('alternative_titles');
        });
    }
};
