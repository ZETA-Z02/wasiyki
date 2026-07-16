<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHabitacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización ya la maneja el middleware auth:sanctum
    }

    public function rules(): array
    {
        return [
            'piso' => 'required|integer',
            'numero' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'estado' => 'sometimes|in:disponible,ocupada,mantenimiento',
        ];
    }
}