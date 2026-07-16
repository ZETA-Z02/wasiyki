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
use Illuminate\Support\Str;

class JerssonSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buscar al usuario Jersson por su email
        $user = User::where('email', 'dota2jersson3@gmail.com')->first();

        if (!$user) {
            // Por si acaso no existiera en otro ambiente local, lo creamos
            $user = User::create([
                'nombre' => 'jersson Pelayo',
                'apellido' => 'Quispe apaza',
                'email' => 'dota2jersson3@gmail.com',
                'telefono' => '999111222',
            ]);
        }

        // 2. Autenticar para que el BelongsToTenant scope asigne/filtre automáticamente por su ID (3)
        Auth::login($user);

        // 3. Limpieza de datos existentes previos para Jersson (para evitar duplicados si se corre varias veces)
        Pago::query()->forceDelete();
        Contrato::query()->forceDelete();
        Inquilino::query()->forceDelete();
        Habitacion::query()->forceDelete();

        // 4. Crear Habitaciones para Jersson
        $hab101 = Habitacion::create(['piso' => 1, 'numero' => '101', 'descripcion' => 'Habitación primer piso frente', 'precio' => 550.00, 'estado' => 'disponible']);
        $hab102 = Habitacion::create(['piso' => 1, 'numero' => '102', 'descripcion' => 'Habitación interior tragaluz', 'precio' => 500.00, 'estado' => 'ocupada']);
        $hab201 = Habitacion::create(['piso' => 2, 'numero' => '201', 'descripcion' => 'Habitación segundo piso vista parque', 'precio' => 750.00, 'estado' => 'ocupada']);
        $hab202 = Habitacion::create(['piso' => 2, 'numero' => '202', 'descripcion' => 'Habitación estándar segundo piso', 'precio' => 700.00, 'estado' => 'disponible']);
        $hab203 = Habitacion::create(['piso' => 2, 'numero' => '203', 'descripcion' => 'Mini-departamento segundo piso', 'precio' => 680.00, 'estado' => 'ocupada']);
        $hab301 = Habitacion::create(['piso' => 3, 'numero' => '301', 'descripcion' => 'Habitación tercer piso baño compartido', 'precio' => 400.00, 'estado' => 'mantenimiento']);

        // 5. Crear Inquilinos para Jersson
        $inqCamila = Inquilino::create([
            'nombre' => 'Camila',
            'apellido' => 'Núñez',
            'telefono' => '999111333',
            'email' => 'camila@example.com',
            'dni' => '44556677',
            'fecha_nacimiento' => '1997-12-03'
        ]);

        $inqAlejandro = Inquilino::create([
            'nombre' => 'Alejandro',
            'apellido' => 'Castro',
            'telefono' => '912888999',
            'email' => 'alejandro@example.com',
            'dni' => '11223344',
            'fecha_nacimiento' => '1994-06-15'
        ]);

        $inqFernando = Inquilino::create([
            'nombre' => 'Fernando',
            'apellido' => 'Soto',
            'telefono' => '988444555',
            'email' => 'fernando@example.com',
            'dni' => '77889900',
            'fecha_nacimiento' => '1989-10-10'
        ]);

        // 6. Crear Contratos para Jersson
        // Contrato 1: Camila (Activo y al día)
        $contratoCamila = Contrato::create([
            'inquilino_id' => $inqCamila->id,
            'habitacion_id' => $hab102->id,
            'canon_mensual' => 500.00,
            'estado_contrato' => 'activo',
            'tipo_contrato' => 'fijo',
            'fecha_inicio' => Carbon::now()->subMonths(3)->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->addMonths(9)->format('Y-m-d'),
        ]);

        // Contrato 2: Alejandro (Con deuda)
        $contratoAlejandro = Contrato::create([
            'inquilino_id' => $inqAlejandro->id,
            'habitacion_id' => $hab201->id,
            'canon_mensual' => 750.00,
            'estado_contrato' => 'con_deuda',
            'tipo_contrato' => 'indefinido',
            'fecha_inicio' => Carbon::now()->subMonths(2)->format('Y-m-d'),
        ]);

        // Contrato 3: Fernando (Activo, toca pagar mañana)
        $contratoFernando = Contrato::create([
            'inquilino_id' => $inqFernando->id,
            'habitacion_id' => $hab203->id,
            'canon_mensual' => 680.00,
            'estado_contrato' => 'activo',
            'tipo_contrato' => 'fijo',
            'fecha_inicio' => Carbon::now()->subMonths(1)->addDay()->format('Y-m-d'),
            'fecha_fin' => Carbon::now()->addMonths(5)->format('Y-m-d'),
        ]);

        // 7. Crear Pagos para Jersson
        // Pagos de Camila (3 pagos en total, el último hoy)
        Pago::create([
            'contrato_id' => $contratoCamila->id,
            'monto' => 500.00,
            'fecha_pago' => Carbon::now()->subMonths(2)->format('Y-m-d'),
            'periodo' => Carbon::now()->subMonths(2)->format('F Y'),
            'metodo_pago' => 'yape',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        Pago::create([
            'contrato_id' => $contratoCamila->id,
            'monto' => 500.00,
            'fecha_pago' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'periodo' => Carbon::now()->subMonths(1)->format('F Y'),
            'metodo_pago' => 'transferencia',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        Pago::create([
            'contrato_id' => $contratoCamila->id,
            'monto' => 500.00,
            'fecha_pago' => Carbon::now()->format('Y-m-d'),
            'periodo' => Carbon::now()->format('F Y'),
            'metodo_pago' => 'yape',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        // Pagos de Alejandro (Solo pagó el mes pasado)
        Pago::create([
            'contrato_id' => $contratoAlejandro->id,
            'monto' => 750.00,
            'fecha_pago' => Carbon::now()->subMonths(1)->format('Y-m-d'),
            'periodo' => Carbon::now()->subMonths(1)->format('F Y'),
            'metodo_pago' => 'transferencia',
            'numero_comprobante' => 'CP-' . strtoupper(Str::random(8)),
        ]);

        // Fernando aún no tiene pagos registrados (su primer cobro es mañana)

        Auth::logout();
    }
}
