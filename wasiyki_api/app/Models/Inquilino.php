<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inquilino extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'inquilinos';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'dni',
        'fecha_nacimiento'
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }
}