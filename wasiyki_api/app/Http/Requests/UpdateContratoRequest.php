<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inquilino_id' => 'sometimes|exists:inquilinos,id',
            'habitacion_id' => 'sometimes|exists:habitaciones,id',
            'canon_mensual' => 'sometimes|numeric|min:0',
            'estado_contrato' => 'sometimes|in:activo,finalizado,con_deuda',
            'tipo_contrato' => 'sometimes|in:fijo,indefinido',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ];
    }
}