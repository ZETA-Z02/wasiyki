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
        Schema::create('inquilinos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrendador_id')->constrained('users')->onDelete('cascade'); // Multi-tenant

            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('dni')->unique();
            $table->date('fecha_nacimiento')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquilinos');
    }
};
