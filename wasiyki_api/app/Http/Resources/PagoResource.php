<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'monto' => (float) $this->monto,
            'fecha_pago' => $this->fecha_pago ? $this->fecha_pago->format('Y-m-d') : null,
            'periodo' => $this->periodo,
            'metodo_pago' => $this->metodo_pago,
            'numero_comprobante' => $this->numero_comprobante,
            'observaciones' => $this->observaciones,

            'contrato' => new ContratoResource($this->whenLoaded('contrato')),
            'contrato_id' => $this->contrato_id,
        ];
    }
}