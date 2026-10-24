<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

// La documentación OpenAPI de estas rutas está en app/Swagger/Rutas
class AuthController extends Controller
{
    /**
     * Manejar el inicio de sesión de la API.
     */
    public function iniciarSesion(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'clave' => 'required|string',
        ]);

        // Verificación directa de la clave (API sin estado, no usa el guard de sesión)
        $usuario = Usuario::where('usuario', $request->usuario)->first();

        if (! $usuario || ! Hash::check($request->clave, $usuario->clave)) {
            return response()->json([
                'mensaje' => 'Las credenciales proporcionadas son incorrectas.',
            ], 401);
        }

        if (! $usuario->activo) {
            return response()->json([
                'mensaje' => 'Este usuario se encuentra desactivado.',
            ], 403);
        }

        // Crear el token de acceso con Sanctum
        $token = $usuario->createToken('auth_token')->plainTextToken;

        // 🚀 OBTENER ROLES Y PERMISOS DIRECTOS DESDE SPATIE (Guard api)
        $roles = $usuario->getRoleNames(); // Devuelve un array con los nombres de sus roles
        
        // Obtenemos todos los nombres de sus permisos asignados (por rol o directamente)
        $permisos = $usuario->getAllPermissions()->pluck('name');

        // Evaluamos si cuenta con el permiso commodore global para facilitarle la lógica a React
        $esSuperAdmin = $usuario->hasPermissionTo('acceso_total', 'api');

        return response()->json([
            'mensaje' => 'Inicio de sesión exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'usuario' => $usuario->usuario,
                'es_admin' => $esSuperAdmin,    // Redundancia útil para el renderizado en React
                'roles' => $roles,              // Ej: ['auxiliar_almacen']
                'permisos' => $permisos,        // Ej: ['activo_listar', 'activo_guardar']
            ],
        ], 200);
    }

    /**
     * Cierre de sesión (Revocar el token actual).
     */
    public function cerrarSesion(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensaje' => 'Sesión cerrada correctamente y token eliminado.',
        ], 200);
    }
}