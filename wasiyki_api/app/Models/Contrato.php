<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrato extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'contratos';

    protected $fillable = [
        'inquilino_id',
        'habitacion_id',
        'canon_mensual',
        'estado_contrato',
        'tipo_contrato',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    // Relaciones para poder obtener los datos en el API
    public function inquilino(): BelongsTo
    {
        return $this->belongsTo(Inquilino::class);
    }

    public function habitacion(): BelongsTo
    {
        return $this->belongsTo(Habitacion::class);
    }
}