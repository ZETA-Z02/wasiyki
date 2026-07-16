<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Habitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ContratoResource;
use App\Http\Requests\StoreContratoRequest;
use App\Http\Requests\UpdateContratoRequest;

class ContratoController extends Controller
{
    // Listar contratos (Cargando relaciones para el frontend)
    public function index()
    {
        $contratos = Contrato::with(['inquilino', 'habitacion'])->get();
        return ContratoResource::collection($contratos);
    }

    // Guardar nuevo contrato y ocupar la habitación
    public function store(StoreContratoRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $habitacion = Habitacion::findOrFail($request->habitacion_id);

            // Validar que la habitación esté libre
            if ($habitacion->estado !== 'disponible') {
                abort(422, 'La habitación seleccionada no está disponible.');
            }

            // Crear contrato (El TenantScope inyecta el arrendador_id)
            $contrato = Contrato::create($request->validated());

            // Cambiar estado de la habitación
            $habitacion->update(['estado' => 'ocupada']);

            return new ContratoResource($contrato->load(['inquilino', 'habitacion']));
        });
    }

    // Mostrar un contrato específico
    public function show(Contrato $contrato)
    {
        return new ContratoResource($contrato->load(['inquilino', 'habitacion']));
    }

    // Actualizar contrato
    public function update(UpdateContratoRequest $request, Contrato $contrato)
    {
        $contrato = DB::transaction(function () use ($request, $contrato) {
            $contrato->update($request->validated());

            // Si el estado del contrato cambia a finalizado, liberamos la habitación
            if ($request->has('estado_contrato') && $request->estado_contrato === 'finalizado') {
                $habitacion = Habitacion::findOrFail($contrato->habitacion_id);
                $habitacion->update(['estado' => 'disponible']);
            }

            return $contrato;
        });

        return new ContratoResource($contrato->load(['inquilino', 'habitacion']));
    }

    // Terminar contrato (Liberar habitación) - Endpoint personalizado
    public function terminar(Contrato $contrato)
    {
        DB::transaction(function () use ($contrato) {
            $contrato->update([
                'estado_contrato' => 'finalizado',
                'fecha_fin' => now() // Cierra el contrato el día de hoy
            ]);

            // Liberar habitación
            if ($contrato->habitacion) {
                $contrato->habitacion->update(['estado' => 'disponible']);
            }
        });

        return response()->json(['message' => 'Contrato finalizado y habitación liberada correctamente.']);
    }

    // Eliminar contrato (Soft Delete)
    public function destroy(Contrato $contrato)
    {
        DB::transaction(function () use ($contrato) {
            // Liberamos la habitación antes de eliminar lógicamente el contrato
            $habitacion = Habitacion::findOrFail($contrato->habitacion_id);
            $habitacion->update(['estado' => 'disponible']);

            $contrato->delete();
        });

        return response()->json(['message' => 'Contrato finalizado/eliminado correctamente']);
    }
}