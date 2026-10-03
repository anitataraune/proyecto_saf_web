<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    // Listar usuarios (Filtrable para incluir eliminados de la BD)
    public function listar(Request $request)
    {
        $query = Usuario::query();

        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return response()->json($query->get(), 200);
    }

    // Crear Usuario
    public function guardar(Request $request)
    {
        $validado = $request->validate([
            'usuario' => 'required|string|unique:usuario,usuario',
            'clave' => 'required|string|min:6',
        ]);

        $usuario = Usuario::create($validado);

        return response()->json([
            'mensaje' => 'Usuario creado con éxito',
            'data' => $usuario,
        ], 201);
    }

    // Mostrar un usuario específico
    public function mostrar($id)
    {
        $usuario = Usuario::withTrashed()->findOrFail($id);

        return response()->json($usuario, 200);
    }

    // Actualizar usuario
    public function actualizar(Request $request, $id)
    {
        $usuario = Usuario::withTrashed()->findOrFail($id);

        $validado = $request->validate([
            'usuario' => 'sometimes|string|unique:usuario,usuario,'.$id.',id_usuario',
            'clave' => 'sometimes|string|min:6',
        ]);

        $usuario->update($validado);

        return response()->json([
            'mensaje' => 'Usuario actualizado con éxito',
            'data' => $usuario,
        ], 200);
    }

    // --- MANEJO DE ESTADOS (ACTIVO / DESACTIVO) ---

    public function activar($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update(['activo' => true]);

        return response()->json(['mensaje' => 'Usuario activado correctamente'], 200);
    }

    public function desactivar($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->update(['activo' => false]);

        return response()->json(['mensaje' => 'Usuario desactivado correctamente (Acceso restringido)'], 200);
    }

    // --- MANEJO DE ELIMINADO LÓGICO (SOFT DELETES) ---

    // Eliminado lógico (Manda a Soft Delete)
    public function eliminar($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['mensaje' => 'Usuario enviado a papelera (Soft Deleted)'], 200);
    }

    // Restaurar de Soft Delete
    public function restaurar($id)
    {
        $usuario = Usuario::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return response()->json(['mensaje' => 'Usuario restaurado con éxito'], 200);
    }
}
