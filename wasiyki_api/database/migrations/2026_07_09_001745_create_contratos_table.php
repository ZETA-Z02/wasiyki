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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrendador_id')->constrained('users')->onDelete('cascade'); // Multi-tenant
            $table->foreignId('inquilino_id')->constrained('inquilinos')->onDelete('cascade');
            $table->foreignId('habitacion_id')->constrained('habitaciones')->onDelete('restrict');

            // Inmueble_id lo omitimos por ahora a menos que un arrendador tenga MÚLTIPLES casas. 
            // Si solo tiene una, el arrendador_id ya representa su negocio.

            $table->decimal('canon_mensual', 10, 2);
            $table->enum('estado_contrato', ['activo', 'finalizado', 'con_deuda'])->default('activo');
            $table->enum('tipo_contrato', ['fijo', 'indefinido'])->default('indefinido');

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable(); // Será null si es indefinido

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
