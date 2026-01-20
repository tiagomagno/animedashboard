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
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mal_id')->unique();
            $table->foreignId('season_id')->constrained()->onDelete('cascade');
            
            // Dados básicos
            $table->string('title');
            $table->text('synopsis')->nullable();
            $table->string('main_picture_medium')->nullable();
            $table->string('main_picture_large')->nullable();
            
            // Métricas MAL
            $table->decimal('mean', 4, 2)->nullable(); // Score médio
            $table->integer('num_list_users')->default(0); // Número de membros
            $table->integer('popularity')->nullable();
            $table->integer('rank')->nullable();
            
            // Informações adicionais
            $table->string('status')->nullable(); // finished_airing, currently_airing, not_yet_aired
            $table->string('media_type')->nullable(); // tv, movie, ova, special, ona, music
            $table->integer('num_episodes')->nullable();
            $table->json('genres')->nullable();
            
            // Controle
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('mal_id');
            $table->index('season_id');
            $table->index('mean');
            $table->index('popularity');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
