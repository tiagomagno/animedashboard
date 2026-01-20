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
        Schema::create('anime_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained()->onDelete('cascade');
            
            // Métricas históricas
            $table->decimal('mean', 4, 2)->nullable();
            $table->integer('num_list_users')->default(0);
            $table->integer('popularity')->nullable();
            $table->integer('rank')->nullable();
            
            // Timestamp da coleta
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            // Índices
            $table->index('anime_id');
            $table->index('recorded_at');
            $table->index(['anime_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anime_stats');
    }
};
