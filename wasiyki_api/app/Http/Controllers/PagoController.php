<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Contrato;
use App\Http\Requests\StorePagoRequest;
use App\Http\Requests\UpdatePagoRequest;
use App\Http\Resources\PagoResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PagoController extends Controller
{
    public function index()
    {
        // Traemos los pagos con la información del contrato (y su inquilino) cargada
        $pagos = Pago::with(['contrato.inquilino'])->get();
        return PagoResource::collection($pagos);
    }

    public function store(StorePagoRequest $request)
    {
        $pago = DB::transaction(function () use ($request) {
            $data = $request->validated();

            // Generar un número de comprobante automático si no se provee uno
            if (empty($data['numero_comprobante'])) {
                $data['numero_comprobante'] = 'CP-' . strtoupper(Str::random(8));
            }

            $nuevoPago = Pago::create($data);

            // Opcional: Si el contrato estaba "con_deuda", pasarlo a "activo" al recibir un pago
            $contrato = Contrato::find($data['contrato_id']);
            if ($contrato && $contrato->estado_contrato === 'con_deuda') {
                $contrato->update(['estado_contrato' => 'activo']);
            }

            return $nuevoPago;
        });

        return new PagoResource($pago->load('contrato'));
    }

    public function show(Pago $pago)
    {
        return new PagoResource($pago->load('contrato.inquilino'));
    }

    public function update(UpdatePagoRequest $request, Pago $pago)
    {
        $pago->update($request->validated());
        return new PagoResource($pago->load('contrato'));
    }

    public function destroy(Pago $pago)
    {
        $pago->delete();
        return response()->json(['message' => 'Pago eliminado correctamente']);
    }

    public function generarComprobante(Pago $pago)
    {
        // Cargamos todas las relaciones necesarias para el recibo
        $pago->load(['contrato.inquilino', 'contrato.habitacion']);

        // Obtenemos al arrendador (usuario autenticado) para los datos de la cabecera
        $arrendador = auth()->user();

        // Generamos el PDF usando una vista Blade
        $pdf = Pdf::loadView('pdf.comprobante', [
            'pago' => $pago,
            'arrendador' => $arrendador
        ]);

        // Retornamos el PDF para que el frontend (React) lo pueda descargar o visualizar
        return $pdf->download('comprobante_' . $pago->numero_comprobante . '.pdf');
    }
}