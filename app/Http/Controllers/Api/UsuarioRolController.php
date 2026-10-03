<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioRolController extends Controller
{
    /**
     * Listar todas las asignaciones de roles actuales en el sistema.
     */
    public function listar()
    {
        // Seleccionamos explícitamente tus campos personalizados: id_usuario y usuario
        $asignaciones = Usuario::select('id_usuario', 'usuario') 
            ->with('roles:id,name')
            ->has('roles')
            ->get();

        // Mapeamos usando las propiedades exactas de tu modelo
        $resultado = $asignaciones->map(function ($user) {
            return [
                'usuario_id'     => $user->id_usuario, // Tu clave primaria
                'usuario_nombre' => $user->usuario,    // Tu campo de nombre de usuario
                'roles'          => $user->roles->map(function ($rol) {
                    return [
                        'id'   => $rol->id,
                        'name' => $rol->name
                    ];
                })
            ];
        });

        return response()->json($resultado, 200);
    }

    /**
     * Registrar una nueva asignación de roles para un usuario.
     */
    public function guardar(Request $request)
    {
        $request->validate([
            // Validamos contra tu llave primaria 'id_usuario' y tu tabla 'usuario'
            'usuario_id' => 'required|exists:usuario,id_usuario', 
            'roles'      => 'required|array',
            'roles.*'    => 'string|exists:roles,name',
        ]);

        // Buscamos por tu llave primaria personalizada
        $user = Usuario::findOrFail($request->usuario_id);
        
        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Roles asignados correctamente',
            'roles'   => $user->getRoleNames()
        ], 201);
    }

    /**
     * Mostrar los roles específicos asignados a un usuario.
     */
    public function mostrar($id)
    {
        $user = Usuario::with('roles')->findOrFail($id);

        return response()->json([
            'usuario_id'     => $user->id_usuario,
            'usuario_nombre' => $user->usuario,
            'roles_asignados' => $user->roles->pluck('name')
        ], 200);
    }

    /**
     * Actualizar por completo los roles de un usuario.
     */
    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'roles'   => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $user = Usuario::findOrFail($id);
        $user->syncRoles($request->roles);

        return response()->json([
            'message' => 'Asignación de roles actualizada correctamente',
            'roles'   => $user->getRoleNames()
        ], 200);
    }

    /**
     * Eliminar (remover) todos los roles asignados a un usuario.
     */
    public function eliminar($id)
    {
        $user = Usuario::findOrFail($id);
        $user->syncRoles([]);

        return response()->json([
            'message' => 'Se han removido todos los roles del usuario'
        ], 200);
    }
}