<?php

namespace App\Services\Recordatorios;

use App\Models\Inquilino;
use App\Models\Contrato;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailRecordatorioService implements RecordatorioServiceInterface
{
    public function enviar(Inquilino $inquilino, Contrato $contrato, string $mensaje): bool
    {
        if (!$inquilino->email) {
            return false;
        }

        try {
            // Usando la fachada Mail nativa de Laravel (requiere configurar SMTP en .env)
            Mail::raw($mensaje, function ($mail) use ($inquilino) {
                $mail->to($inquilino->email)
                    ->subject('Recordatorio de Pago de Alquiler');
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Error enviando Email: ' . $e->getMessage());
            return false;
        }
    }
}