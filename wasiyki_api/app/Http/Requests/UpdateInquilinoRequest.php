<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInquilinoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID del inquilino de la ruta para ignorarlo en la regla unique
        $inquilinoId = $this->route('inquilino')->id;

        return [
            'nombre' => 'sometimes|string|max:255',
            'apellido' => 'sometimes|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'dni' => 'sometimes|string|unique:inquilinos,dni,' . $inquilinoId,
            'fecha_nacimiento' => 'nullable|date',
        ];
    }
}