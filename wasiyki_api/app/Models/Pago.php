<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'pagos';

    protected $fillable = [
        'contrato_id',
        'monto',
        'fecha_pago',
        'periodo',
        'metodo_pago',
        'numero_comprobante',
        'observaciones'
    ];

    protected function casts(): array
    {
        return [
            'fecha_pago' => 'date',
        ];
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }
}