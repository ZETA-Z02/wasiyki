<?php

namespace App\Http\Controllers;

use App\Models\Inquilino;
use App\Http\Requests\StoreInquilinoRequest;
use App\Http\Requests\UpdateInquilinoRequest;
use App\Http\Resources\InquilinoResource;
use Illuminate\Http\Request;

class InquilinoController extends Controller
{
    // Listar todos los inquilinos del arrendador
    public function index()
    {
        $inquilinos = Inquilino::all();
        return InquilinoResource::collection($inquilinos);
    }

    // Guardar nuevo inquilino
    public function store(StoreInquilinoRequest $request)
    {
        $inquilino = Inquilino::create($request->validated());
        return new InquilinoResource($inquilino);
    }

    // Mostrar un inquilino específico
    public function show(Inquilino $inquilino)
    {
        return new InquilinoResource($inquilino);
    }

    // Actualizar inquilino
    public function update(UpdateInquilinoRequest $request, Inquilino $inquilino)
    {
        $inquilino->update($request->validated());
        return new InquilinoResource($inquilino);
    }

    // Eliminar inquilino (Soft Delete)
    public function destroy(Inquilino $inquilino)
    {
        $inquilino->delete();
        return response()->json(['message' => 'Inquilino eliminado correctamente']);
    }
}