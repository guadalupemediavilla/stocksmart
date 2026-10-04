<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $empleado = Empleado::where('mail', $request->mail)->first();

        if (!$empleado || !password_verify($request->password, $empleado->password)) {
            return response()->json(['mensaje' => 'Credenciales incorrectas'], 401);
        }

        if ($empleado->rol !== 'admin') {
            return response()->json(['mensaje' => 'Solo los administradores pueden usar la API'], 403);
        }

        $token = $empleado->createToken('token-api')->plainTextToken;

        return response()->json([
            'mensaje' => 'Login exitoso',
            'token' => $token,
        ]);
    }
}