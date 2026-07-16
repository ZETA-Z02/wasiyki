<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHabitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'piso' => 'sometimes|integer',
            'numero' => 'sometimes|string|max:50',
            'descripcion' => 'nullable|string',
            'precio' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:disponible,ocupada,mantenimiento',
        ];
    }
}