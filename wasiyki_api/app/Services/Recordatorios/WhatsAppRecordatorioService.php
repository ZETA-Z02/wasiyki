<?php

namespace App\Services\Recordatorios;

use App\Models\Inquilino;
use App\Models\Contrato;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppRecordatorioService implements RecordatorioServiceInterface
{
    public function enviar(Inquilino $inquilino, Contrato $contrato, string $mensaje): bool
    {
        if (!$inquilino->telefono) {
            return false;
        }

        try {
            // Ejemplo de payload para Evolution API
            $response = Http::withHeaders([
                'apikey' => env('EVOLUTION_API_KEY')
            ])->post(env('EVOLUTION_API_URL') . '/message/sendText/' . env('EVOLUTION_INSTANCE_NAME'), [
                        'number' => $inquilino->telefono,
                        'options' => [
                            'delay' => 1200,
                        ],
                        'textMessage' => [
                            'text' => $mensaje
                        ]
                    ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('Error enviando WhatsApp: ' . $e->getMessage());
            return false;
        }
    }
}