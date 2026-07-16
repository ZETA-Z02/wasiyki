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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrendador_id')->constrained('users')->onDelete('cascade'); // Multi-tenant directo
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');

            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('periodo'); // Ejemplo: "Julio 2026" o "2026-07"
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'yape', 'plin', 'otro']);
            $table->string('numero_comprobante')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
