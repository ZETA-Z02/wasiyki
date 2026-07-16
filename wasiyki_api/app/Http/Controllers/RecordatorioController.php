<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use Illuminate\Http\Request;
use App\Services\Recordatorios\RecordatorioServiceInterface;
use App\Services\Recordatorios\WhatsAppRecordatorioService;
use App\Services\Recordatorios\EmailRecordatorioService;

class RecordatorioController extends Controller
{
    public function enviarManual(Request $request, Contrato $contrato)
    {
        $request->validate([
            'canal' => 'required|in:whatsapp,email',
            'mensaje' => 'required|string'
        ]);

        // Cargamos el inquilino para tener sus datos de contacto
        $contrato->load('inquilino');

        // Factory básico: Decidimos qué implementación de la interfaz usar
        $servicio = $this->obtenerServicio($request->canal);

        // Ejecutamos el método de la interfaz sin importar qué servicio es
        $exito = $servicio->enviar($contrato->inquilino, $contrato, $request->mensaje);

        if ($exito) {
            return response()->json(['message' => 'Recordatorio enviado exitosamente por ' . $request->canal]);
        }

        return response()->json(['message' => 'No se pudo enviar el recordatorio. Verifique los datos del inquilino.'], 400);
    }

    /**
     * Devuelve la implementación correcta basada en el string del canal.
     */
    private function obtenerServicio(string $canal): RecordatorioServiceInterface
    {
        return match ($canal) {
            'whatsapp' => new WhatsAppRecordatorioService(),
            'email' => new EmailRecordatorioService(),
            default => throw new \InvalidArgumentException('Canal no soportado'),
        };
    }
}