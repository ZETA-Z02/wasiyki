<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago</title>
    <style>
        /* Configuramos la página para un formato de ticket de 80mm */
        @page {
            margin: 5mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
            background-color: #fff;
            margin: 0 auto;
            width: 100%;
            max-width: 300px;
            /* Ancho máximo para simular el ticket */
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .title {
            font-size: 16px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 14px;
        }

        /* Línea punteada característica de los tickets */
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .solid-line {
            border-top: 1px solid #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .col-label {
            width: 40%;
        }

        .col-value {
            width: 60%;
            text-transform: uppercase;
        }

        .totals-row td {
            font-size: 13px;
            font-weight: bold;
        }

        .items-header td {
            font-weight: bold;
            padding-bottom: 5px;
            border-bottom: 1px solid #000;
        }
    </style>
</head>

<body>

    <!-- Encabezado del Arrendador -->
    <div class="text-center">
        <div class="bold title">{{ $arrendador->nombre }} {{ $arrendador->apellido }}</div>
        <div>{{ $arrendador->email }}</div>
        @if($arrendador->telefono)
            <div>Tel: {{ $arrendador->telefono }}</div>
        @endif
    </div>

    <div class="dashed-line"></div>

    <!-- Título del Documento -->
    <div class="text-center">
        <div class="bold subtitle">RECIBO</div>
        <div>{{ $pago->numero_comprobante }}</div>
    </div>

    <div class="dashed-line"></div>

    <!-- Detalles Generales -->
    <table>
        <tr>
            <td class="col-label">Fecha de Emisión:</td>
            <td class="col-value">{{ now()->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="col-label">Hora de Emisión:</td>
            <td class="col-value">{{ now()->format('h:i A') }}</td>
        </tr>
        <tr>
            <td class="col-label">Cliente:</td>
            <td class="col-value">{{ $pago->contrato->inquilino->nombre }} {{ $pago->contrato->inquilino->apellido }}</td>
        </tr>
        <tr>
            <td class="col-label">DNI:</td>
            <td class="col-value">{{ $pago->contrato->inquilino->dni }}</td>
        </tr>
        <tr>
            <td class="col-label">Habitación:</td>
            <td class="col-value">N° {{ $pago->contrato->habitacion->numero }}</td>
        </tr>
        <tr>
            <td class="col-label">Periodo:</td>
            <td class="col-value">{{ $pago->periodo }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <!-- Total a cobrar -->
    <table>
        <tr class="totals-row">
            <td>TOTAL:</td>
            <td class="text-right">S/. {{ number_format($pago->monto, 2) }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <!-- Detalle de pagos realizados -->
    <table>
        <tr class="items-header">
            <td>FECHA / MÉTODO</td>
            <td class="text-right">MONTO</td>
        </tr>
        <tr>
            <td style="padding-top: 5px;">- {{ $pago->fecha_pago->format('d/m/Y') }} ({{ ucfirst($pago->metodo_pago) }})</td>
            <td class="text-right" style="padding-top: 5px;">S/. {{ number_format($pago->monto, 2) }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <!-- Resumen Final -->
    <table>
        <tr class="totals-row">
            <td>TOTAL PAGADO:</td>
            <td class="text-right">S/. {{ number_format($pago->monto, 2) }}</td>
        </tr>
    </table>

    @if($pago->observaciones)
        <div class="dashed-line"></div>
        <div style="font-size: 11px;">
            <span class="bold">Nota:</span> {{ $pago->observaciones }}
        </div>
    @endif

    <!-- Pie de página -->
    <br><br>
    <div class="text-center">
        <div>Gracias por su preferencia!</div>
        <div class="dashed-line" style="width: 60%; margin: 5px auto;"></div>
    </div>

</body>

</html>