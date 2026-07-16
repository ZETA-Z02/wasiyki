<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Contrato;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // 1. Métricas de Habitaciones
        $habitacionesTotales = Habitacion::count();
        $habitacionesOcupadas = Habitacion::where('estado', 'ocupada')->count();

        // 2. Habitaciones Disponibles ahora (nombres/números)
        $disponibles = Habitacion::where('estado', 'disponible')
            ->pluck('numero')
            ->map(fn($numero) => "Hab. {$numero}")
            ->toArray();

        // 3. Métricas Financieras (Ingresos del mes actual)
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        $ingresosMes = (float) Pago::whereBetween('fecha_pago', [$inicioMes, $finMes])->sum('monto');

        // 4. Alertas y Vencimientos
        $alertas = [];

        // 4a. Alertas de Atraso (Contratos con estado "con_deuda")
        $contratosDeuda = Contrato::with(['inquilino', 'habitacion'])
            ->where('estado_contrato', 'con_deuda')
            ->get();

        foreach ($contratosDeuda as $contrato) {
            $inquilinoNombre = $contrato->inquilino 
                ? $contrato->inquilino->nombre . ' ' . $contrato->inquilino->apellido 
                : 'Inquilino';
            $habitacionNumero = $contrato->habitacion ? $contrato->habitacion->numero : '?';

            $alertas[] = [
                'id' => 'deuda-' . $contrato->id,
                'mensaje' => "{$inquilinoNombre} (Hab. {$habitacionNumero})",
                'tipo' => 'atraso',
                'monto' => 'S/ ' . (float) $contrato->canon_mensual,
            ];
        }

        // 4b. Alertas de Próximo Pago (Contratos activos cuyo día de cobro es inminente)
        $contratosActivos = Contrato::with(['inquilino', 'habitacion'])
            ->where('estado_contrato', 'activo')
            ->get();

        $hoy = Carbon::now();
        foreach ($contratosActivos as $contrato) {
            $diaCobro = $contrato->fecha_inicio->day;
            
            try {
                $vencimiento = Carbon::create($hoy->year, $hoy->month, $diaCobro);
            } catch (\Exception $e) {
                $vencimiento = Carbon::now()->endOfMonth();
            }

            // Si el día ya pasó, el próximo es el siguiente mes
            if ($vencimiento->isPast() && !$vencimiento->isToday()) {
                $vencimiento->addMonth();
            }

            $diasDiferencia = $hoy->diffInDays($vencimiento, false);

            // Alertas para cobros en los siguientes 5 días
            if ($diasDiferencia >= 0 && $diasDiferencia <= 5) {
                $fechaTexto = '';
                if ($diasDiferencia == 0) {
                    $fechaTexto = 'Hoy';
                } elseif ($diasDiferencia == 1) {
                    $fechaTexto = 'Mañana';
                } else {
                    $fechaTexto = $vencimiento->translatedFormat('d M');
                }

                $inquilinoNombre = $contrato->inquilino 
                    ? $contrato->inquilino->nombre . ' ' . $contrato->inquilino->apellido 
                    : 'Inquilino';
                $habitacionNumero = $contrato->habitacion ? $contrato->habitacion->numero : '?';

                $alertas[] = [
                    'id' => 'proximo-' . $contrato->id,
                    'mensaje' => "{$inquilinoNombre} (Hab. {$habitacionNumero})",
                    'tipo' => 'proximo',
                    'fecha' => $fechaTexto
                ];
            }
        }

        // 5. Últimos pagos realizados
        $ultimosPagos = Pago::with(['contrato.inquilino', 'contrato.habitacion'])
            ->orderBy('fecha_pago', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($pago) {
                return [
                    'id' => $pago->id,
                    'inquilino' => $pago->contrato && $pago->contrato->inquilino 
                        ? $pago->contrato->inquilino->nombre . ' ' . $pago->contrato->inquilino->apellido 
                        : 'N/A',
                    'habitacion' => $pago->contrato && $pago->contrato->habitacion 
                        ? 'Hab. ' . $pago->contrato->habitacion->numero 
                        : 'N/A',
                    'monto' => (float) $pago->monto,
                    'fecha' => $pago->fecha_pago ? $pago->fecha_pago->translatedFormat('d M, Y') : '',
                    'metodo' => ucfirst($pago->metodo_pago),
                ];
            });

        // Estructura de datos idéntica a la esperada por el Frontend en React
        return response()->json([
            'habitacionesOcupadas' => $habitacionesOcupadas,
            'habitacionesTotales' => $habitacionesTotales,
            'ingresosMes' => $ingresosMes,
            'disponibles' => $disponibles,
            'alertas' => $alertas,
            'ultimosPagos' => $ultimosPagos,
        ]);
    }
}