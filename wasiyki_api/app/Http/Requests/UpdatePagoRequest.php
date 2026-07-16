<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contrato_id' => 'sometimes|exists:contratos,id',
            'monto' => 'sometimes|numeric|min:0.01',
            'fecha_pago' => 'sometimes|date',
            'periodo' => 'sometimes|string|max:100',
            'metodo_pago' => 'sometimes|in:efectivo,transferencia,yape,plin,otro',
            'numero_comprobante' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ];
    }
}