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
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->enum('season', ['winter', 'spring', 'summer', 'fall']);
            $table->string('name')->nullable(); // Nome customizado da temporada
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
            
            // Índices
            $table->unique(['year', 'season']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
