<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContratoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'canon_mensual' => (float) $this->canon_mensual,
            'estado_contrato' => $this->estado_contrato,
            'tipo_contrato' => $this->tipo_contrato,
            'fecha_inicio' => $this->fecha_inicio ? $this->fecha_inicio->format('Y-m-d') : null,
            'fecha_fin' => $this->fecha_fin ? $this->fecha_fin->format('Y-m-d') : null,

            // Relaciones (Cargadas condicionalmente para optimizar consultas)
            'inquilino' => new InquilinoResource($this->whenLoaded('inquilino')),
            'habitacion' => new HabitacionResource($this->whenLoaded('habitacion')),

            // Si solo necesitas los IDs cuando no cargas la relación completa
            'inquilino_id' => $this->inquilino_id,
            'habitacion_id' => $this->habitacion_id,
        ];
    }
}