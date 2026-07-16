<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Http\Requests\StoreHabitacionRequest;
use App\Http\Requests\UpdateHabitacionRequest;
use App\Http\Resources\HabitacionResource;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    // Listar todas las habitaciones del arrendador
    public function index()
    {
        // El TenantScope filtra automáticamente por arrendador_id
        $habitaciones = Habitacion::all();
        return HabitacionResource::collection($habitaciones);
    }

    // Guardar nueva habitación
    public function store(StoreHabitacionRequest $request)
    {
        // El trait BelongsToTenant inyecta el arrendador_id al crear
        $habitacion = Habitacion::create($request->validated());
        return new HabitacionResource($habitacion);
    }

    // Mostrar una habitación específica
    public function show(Habitacion $habitacion)
    {
        return new HabitacionResource($habitacion);
    }

    // Actualizar habitación
    public function update(UpdateHabitacionRequest $request, Habitacion $habitacion)
    {
        $habitacion->update($request->validated());
        return new HabitacionResource($habitacion);
    }

    // Eliminar habitación (Soft Delete)
    public function destroy(Habitacion $habitacion)
    {
        $habitacion->delete();
        return response()->json(['message' => 'Habitación eliminada correctamente']);
    }

    // Endpoint extra: Obtener solo las disponibles
    public function disponibles()
    {
        $habitaciones = Habitacion::where('estado', 'disponible')->get();
        return HabitacionResource::collection($habitaciones);
    }
}