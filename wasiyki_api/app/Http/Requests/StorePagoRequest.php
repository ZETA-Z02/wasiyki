<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contrato_id' => 'required|exists:contratos,id',
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'periodo' => 'required|string|max:100', // Ej: "Julio 2026"
            'metodo_pago' => 'required|in:efectivo,transferencia,yape,plin,otro',
            'numero_comprobante' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ];
    }
}