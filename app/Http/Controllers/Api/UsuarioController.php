<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarUsuarioRequest;
use App\Http\Requests\GuardarUsuarioRequest;
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

        return $this->responderLista($query, $request);
    }

    // Crear Usuario
    public function guardar(GuardarUsuarioRequest $request)
    {
        $validado = $request->validated();

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
    public function actualizar(ActualizarUsuarioRequest $request, $id)
    {
        $usuario = Usuario::withTrashed()->findOrFail($id);

        $validado = $request->validated();

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

    public function desactivar(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->id_usuario === $request->user()->id_usuario) {
            return response()->json(['mensaje' => 'No puede desactivar su propio usuario.'], 422);
        }

        $usuario->update(['activo' => false]);

        // Revocar sus tokens para cortar el acceso inmediatamente
        $usuario->tokens()->delete();

        return response()->json(['mensaje' => 'Usuario desactivado correctamente (Acceso restringido)'], 200);
    }

    // --- MANEJO DE ELIMINADO LÓGICO (SOFT DELETES) ---

    // Eliminado lógico (Manda a Soft Delete)
    public function eliminar(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->id_usuario === $request->user()->id_usuario) {
            return response()->json(['mensaje' => 'No puede eliminar su propio usuario.'], 422);
        }

        $usuario->tokens()->delete();
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
