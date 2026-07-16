<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Resources\ArrendadorResource;
use App\Http\Requests\UpdateArrendadorRequest;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    // 1. Login con contraseña
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return response()->json([
            'token' => $user->createToken('react-frontend')->plainTextToken,
            'arrendador' => new ArrendadorResource($user)
        ]);
    }

    // 2. Redirección a Google (OAuth)
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    // 3. Callback de Google (OAuth)
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'nombre' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                'apellido' => $googleUser->user['family_name'] ?? '',
                'google_id' => $googleUser->getId(),
            ]
        );

        return response()->json([
            'token' => $user->createToken('react-frontend')->plainTextToken,
            'arrendador' => new ArrendadorResource($user)
        ]);
    }
    /**
     * Endpoint para procesar el Authorization Code + PKCE desde React/Móvil
     */
    public function handleGooglePKCE(Request $request)
    {
        // 1. Relajamos las reglas de validación
        $request->validate([
            'code' => 'required|string',
            'redirect_uri' => 'required|string', // Cambiado de 'url' a 'string'
            'code_verifier' => 'nullable|string', // Cambiado a 'nullable'
        ]);

        // 2. Preparamos el payload base
        $payload = [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'code' => $request->code,
            'redirect_uri' => $request->redirect_uri,
        ];

        // Solo agregamos el code_verifier si realmente fue enviado
        if ($request->filled('code_verifier')) {
            $payload['code_verifier'] = $request->code_verifier;
        }

        // 3. Intercambiar el código en Google
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', $payload);

        if ($response->failed()) {
            return response()->json([
                'message' => 'Error de autenticación federada con Google',
                'details' => $response->json()
            ], 401);
        }

        $googleTokens = $response->json();

        // Obtener la identidad del usuario
        $googleUser = Socialite::driver('google')->stateless()->userFromToken($googleTokens['access_token']);

        // Crear o actualizar el Tenant (Arrendador)
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'nombre' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                'apellido' => $googleUser->user['family_name'] ?? '',
                'google_id' => $googleUser->getId(),
            ]
        );

        // Emitir token de Sanctum
        $token = $user->createToken('react-frontend')->plainTextToken;

        return response()->json([
            'token' => $token,
            'arrendador' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email
            ]
        ]);
    }

    // 4. Obtener datos del Arrendador autenticado
    public function me(Request $request)
    {
        return new ArrendadorResource($request->user());
    }

    // 5. Actualizar datos del Arrendador
    public function update(UpdateArrendadorRequest $request)
    {
        $user = $request->user();

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update($request->except('password'));

        return new ArrendadorResource($user);
    }

    // 6. Cerrar sesión
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}