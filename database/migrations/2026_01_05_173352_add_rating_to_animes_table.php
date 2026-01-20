<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->string('rating')->nullable()->after('media_type'); // g, pg, pg_13, r, r+, rx
        });
    }

    public function down(): void
    {
        Schema::table('animes', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
