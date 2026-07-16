<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Pago</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table th,
        .details-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .details-table th {
            background-color: #f4f4f4;
            width: 35%;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #777;
        }

        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1 class="title">Comprobante de Pago</h1>
        <p><strong>N° Comprobante:</strong> {{ $pago->numero_comprobante }}</p>
    </div>

    <table class="details-table">
        <tr>
            <th>Arrendador</th>
            <td>{{ $arrendador->nombre }} {{ $arrendador->apellido }}</td>
        </tr>
        <tr>
            <th>Inquilino</th>
            <td>{{ $pago->contrato->inquilino->nombre }} {{ $pago->contrato->inquilino->apellido }}</td>
        </tr>
        <tr>
            <th>Habitación</th>
            <td>N° {{ $pago->contrato->habitacion->numero }} (Piso {{ $pago->contrato->habitacion->piso }})</td>
        </tr>
    </table>

    <table class="details-table">
        <tr>
            <th>Fecha de Pago</th>
            <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Período Pagado</th>
            <td>{{ $pago->periodo }}</td>
        </tr>
        <tr>
            <th>Método de Pago</th>
            <td>{{ ucfirst($pago->metodo_pago) }}</td>
        </tr>
        <tr>
            <th>Monto Recibido</th>
            <td class="amount">${{ number_format($pago->monto, 2) }}</td>
        </tr>
    </table>

    @if($pago->observaciones)
        <div>
            <strong>Observaciones:</strong>
            <p>{{ $pago->observaciones }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Este documento es un comprobante de pago válido emitido por el sistema.</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>

</html>