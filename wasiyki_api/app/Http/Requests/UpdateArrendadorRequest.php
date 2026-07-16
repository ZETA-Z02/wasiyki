<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArrendadorRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo puede actualizar si está autenticado
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:20',
            // Valida que el email sea único, excepto para el mismo usuario
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'password' => 'sometimes|string|min:8',
        ];
    }
}