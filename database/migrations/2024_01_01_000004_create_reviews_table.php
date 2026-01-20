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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anime_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Critérios de avaliação (0-10)
            $table->decimal('score_story', 3, 1)->nullable(); // Roteiro
            $table->decimal('score_direction', 3, 1)->nullable(); // Direção
            $table->decimal('score_animation', 3, 1)->nullable(); // Animação
            $table->decimal('score_soundtrack', 3, 1)->nullable(); // Trilha sonora
            $table->decimal('score_impact', 3, 1)->nullable(); // Impacto geral
            
            // Nota final (calculada automaticamente)
            $table->decimal('final_score', 4, 2)->nullable();
            
            // Peso dos critérios (opcional, para cálculo ponderado)
            $table->json('weights')->nullable();
            
            // Comentários
            $table->text('notes')->nullable();
            $table->boolean('is_published')->default(false);
            
            $table->timestamps();
            
            // Índices
            $table->index('anime_id');
            $table->index('user_id');
            $table->index('final_score');
            $table->unique(['anime_id', 'user_id']); // Uma review por usuário por anime
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
