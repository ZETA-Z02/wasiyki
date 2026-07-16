<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Habitacion extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $table = 'habitaciones';

    protected $fillable = [
        'piso',
        'numero',
        'descripcion',
        'precio',
        'estado'
    ];
}