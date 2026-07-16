<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Habitacion;
use App\Models\Inquilino;
use App\Models\Contrato;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el Arrendador de prueba
        $arrendador = User::create([
            'nombre' => 'Carlos',
            'apellido' => 'Administrador',
            'email' => 'carlos@demo.com',
            'password' => Hash::make('password123'),
            'telefono' => '999888777',
        ]);

        // Autenticamos al arrendador para que el Trait BelongsToTenant 
        // inyecte automáticamente el arrendador_id en los demás modelos.
        Auth::login($arrendador);

        // 2. Crear Habitaciones
        $habitacion1 = Habitacion::create(['piso' => 1, 'numero' => '101', 'descripcion' => 'Habitación con ventana a la calle', 'precio' => 500.00, 'estado' => 'ocupada']);
        $habitacion2 = Habitacion::create(['piso' => 1, 'numero' => '102', 'descripcion' => 'Habitación interior', 'precio' => 450.00, 'estado' => 'ocupada']);
        $habitacion3 = Habitacion::create(['piso' => 2, 'numero' => '201', 'descripcion' => 'Habitación doble grande', 'precio' => 700.00, 'estado' => 'disponible']);
        $habitacion4 = Habitacion::create(['piso' => 2, 'numero' => '202', 'descripcion' => 'Habitación pequeña', 'precio' => 350.00, 'estado' => 'mantenimiento']);

        // 3. Crear Inquilinos
        $inquilino1 = Inquilino::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'telefono' => '987654321',
            'email' => 'juan@example.com',
            'dni' => '12345678',
            'fecha_nacimiento' => '1998-05-15'
        ]);

        $inquilino2 = Inquilino::create([
            'nombre' => 'María',
            'apellido' => 'Gómez',
            'telefono' => '912345678',
            'email' => 'maria@example.com',
            'dni' => '87654321',
            'fecha_nacimiento' => '2000-11-22'
        ]);

        // 4. Crear Contratos
        $contrato1 = Contrato::create([
            'inquilino_id' => $inquilino1->id,
            'habitacion_id' => $habitacion1->id,
            'canon_mensual' => 500.00,
            'estado_contrato' => 'activo',
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => Carbon::now()->subMonths(2)->format('Y-m-d'),
        ]);

        $contrato2 = Contrato::create([
            'inquilino_id' => $inquilino2->id,
            'habitacion_id' => $habitacion2->id,
            'canon_mensual' => 450.00,
            'estado_contrato' => 'con_deuda',
            'tipo_contrato' => 'fijo',
            'fecha_inicio' => Carbon::now()->subMonth()->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->addMonths(5)->format('Y-m-d'),
        ]);

        // 5. Crear Pagos (Solo para el contrato 1, dejamos al contrato 2 con deuda)
        Pago::create([
            'contrato_id' => $contrato1->id,
            'monto' => 500.00,
            'fecha_pago' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'periodo' => Carbon::now()->subMonths(1)->format('F Y'),
            'metodo_pago' => 'yape',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
            'observaciones' => 'Pago puntual'
        ]);

        Pago::create([
            'contrato_id' => $contrato1->id,
            'monto' => 500.00,
            'fecha_pago' => Carbon::now()->format('Y-m-d'),
            'periodo' => Carbon::now()->format('F Y'),
            'metodo_pago' => 'transferencia',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        Auth::logout();

        // 6. Crear el Arrendador de prueba 2 (Usuario 2)
        $arrendador2 = User::create([
            'nombre' => 'Lucía',
            'apellido' => 'Valenzuela',
            'email' => 'lucia@demo.com',
            'password' => Hash::make('password123'),
            'telefono' => '988777666',
        ]);

        // Autenticamos al arrendador 2 para inyectar su ID
        Auth::login($arrendador2);

        // 7. Crear Habitaciones para Arrendador 2
        $habPremium = Habitacion::create(['piso' => 3, 'numero' => '301', 'descripcion' => 'Habitación premium con balcón', 'precio' => 800.00, 'estado' => 'ocupada']);
        $habEstudio = Habitacion::create(['piso' => 3, 'numero' => '302', 'descripcion' => 'Habitación estudio interior', 'precio' => 650.00, 'estado' => 'ocupada']);
        $habAtico   = Habitacion::create(['piso' => 4, 'numero' => '401', 'descripcion' => 'Ático con terraza grande', 'precio' => 1200.00, 'estado' => 'disponible']);
        $habSimple  = Habitacion::create(['piso' => 4, 'numero' => '402', 'descripcion' => 'Habitación simple económica', 'precio' => 400.00, 'estado' => 'ocupada']);
        $habLocal   = Habitacion::create(['piso' => 1, 'numero' => '103', 'descripcion' => 'Local comercial planta baja', 'precio' => 1500.00, 'estado' => 'mantenimiento']);

        // 8. Crear Inquilinos para Arrendador 2
        $inq1 = Inquilino::create([
            'nombre' => 'Roberto',
            'apellido' => 'Díaz',
            'telefono' => '987222333',
            'email' => 'roberto@example.com',
            'dni' => '22334455',
            'fecha_nacimiento' => '1990-08-25'
        ]);

        $inq2 = Inquilino::create([
            'nombre' => 'Sandra',
            'apellido' => 'Rojas',
            'telefono' => '912444555',
            'email' => 'sandra@example.com',
            'dni' => '66778899',
            'fecha_nacimiento' => '1995-02-14'
        ]);

        $inq3 = Inquilino::create([
            'nombre' => 'Miguel',
            'apellido' => 'Torres',
            'telefono' => '999333444',
            'email' => 'miguel@example.com',
            'dni' => '55443322',
            'fecha_nacimiento' => '1992-07-07'
        ]);

        // 9. Crear Contratos para Arrendador 2
        // Contrato 1: Activo y al día
        $contratoInq1 = Contrato::create([
            'inquilino_id' => $inq1->id,
            'habitacion_id' => $habPremium->id,
            'canon_mensual' => 800.00,
            'estado_contrato' => 'activo',
            'tipo_contrato' => 'fijo',
            'fecha_inicio' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->addMonths(9)->format('Y-m-d'),
        ]);

        // Contrato 2: Con deuda (mes actual sin pagar)
        $contratoInq2 = Contrato::create([
            'inquilino_id' => $inq2->id,
            'habitacion_id' => $habEstudio->id,
            'canon_mensual' => 650.00,
            'estado_contrato' => 'con_deuda',
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => Carbon::now()->subMonths(2)->format('Y-m-d'),
        ]);

        // Contrato 3: Activo, vence/toca pagar mañana (el día de cobro es el día de mañana)
        $contratoInq3 = Contrato::create([
            'inquilino_id' => $inq3->id,
            'habitacion_id' => $habSimple->id,
            'canon_mensual' => 400.00,
            'estado_contrato' => 'activo',
            'tipo_contrato' => 'fijo',
            'fecha_inicio' => Carbon::now()->subMonths(1)->addDay()->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->addMonths(5)->format('Y-m-d'),
        ]);

        // 10. Crear Pagos para Arrendador 2
        // Pagos de Roberto (3 meses pagados)
        Pago::create([
            'contrato_id' => $contratoInq1->id,
            'monto' => 800.00,
            'fecha_pago' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            'periodo' => Carbon::now()->subMonths(2)->format('F Y'),
            'metodo_pago' => 'transferencia',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        Pago::create([
            'contrato_id' => $contratoInq1->id,
            'monto' => 800.00,
            'fecha_pago' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'periodo' => Carbon::now()->subMonths(1)->format('F Y'),
            'metodo_pago' => 'plin',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        Pago::create([
            'contrato_id' => $contratoInq1->id,
            'monto' => 800.00,
            'fecha_pago' => Carbon::now()->format('Y-m-d'),
            'periodo' => Carbon::now()->format('F Y'),
            'metodo_pago' => 'transferencia',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        // Pagos de Sandra (Solo pagó el mes pasado, el mes de ahora no ha pagado)
        Pago::create([
            'contrato_id' => $contratoInq2->id,
            'monto' => 650.00,
            'fecha_pago' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'periodo' => Carbon::now()->subMonths(1)->format('F Y'),
            'metodo_pago' => 'yape',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        // Cerramos la sesión simulada
        Auth::logout();
    }
}