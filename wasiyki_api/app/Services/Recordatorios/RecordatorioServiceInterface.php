<?php

namespace App\Services\Recordatorios;

use App\Models\Inquilino;
use App\Models\Contrato;

interface RecordatorioServiceInterface
{
    /**
     * Envía un recordatorio de pago al inquilino.
     */
    public function enviar(Inquilino $inquilino, Contrato $contrato, string $mensaje): bool;
}