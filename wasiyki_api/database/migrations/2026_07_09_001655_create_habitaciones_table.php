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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrendador_id')->constrained('users')->onDelete('cascade'); // Multi-tenant

            $table->integer('piso'); // Manejado como atributo numérico simple
            $table->string('numero');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->enum('estado', ['disponible', 'ocupada', 'mantenimiento'])->default('disponible');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
