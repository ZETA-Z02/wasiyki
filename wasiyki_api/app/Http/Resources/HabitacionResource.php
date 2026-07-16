<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HabitacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'piso' => $this->piso,
            'numero' => $this->numero,
            'descripcion' => $this->descripcion,
            'precio' => (float) $this->precio,
            'estado' => $this->estado,
        ];
    }
}