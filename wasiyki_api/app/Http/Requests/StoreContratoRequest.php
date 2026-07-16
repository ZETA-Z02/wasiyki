<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContratoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validamos que el ID exista en la tabla respectiva
            'inquilino_id' => 'required|exists:inquilinos,id',
            'habitacion_id' => 'required|exists:habitaciones,id',
            'canon_mensual' => 'required|numeric|min:0',
            'estado_contrato' => 'sometimes|in:activo,finalizado,con_deuda',
            'tipo_contrato' => 'required|in:fijo,indefinido',
            'fecha_inicio' => 'required|date',
            // La fecha fin es obligatoria solo si el contrato es fijo
            'fecha_fin' => 'required_if:tipo_contrato,fijo|nullable|date|after_or_equal:fecha_inicio',
        ];
    }
}