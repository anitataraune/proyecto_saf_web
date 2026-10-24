<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarRolRequest;
use App\Http\Requests\GuardarRolRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\JsonResponse;

class RolController extends Controller
{
    public function listar(): JsonResponse
    {
        // Traemos los roles cargando sus permisos relacionados
        $roles = Role::with('permissions')->get();
        return response()->json($roles, 200);
    }

    public function guardar(GuardarRolRequest $request): JsonResponse
    {
        $validado = $request->validated();

        // Creamos el rol especificando el guard correspondiente (usualmente 'api' o 'web')
        $rol = Role::create([
            'name' => $validado['name'],
            'guard_name' => 'api'
        ]);

        // Sincroniza los permisos si vienen en la petición
        if (!empty($validado['permisos'])) {
            $rol->syncPermissions($validado['permisos']);
        }

        return response()->json([
            'mensaje' => 'Rol registrado con éxito',
            'data' => $rol->load('permissions')
        ], 201);
    }

    public function mostrar($id): JsonResponse
    {
        $rol = Role::with('permissions')->findOrFail($id);
        return response()->json($rol, 200);
    }

    public function actualizar(ActualizarRolRequest $request, $id): JsonResponse
    {
        $rol = Role::findOrFail($id);

        $validado = $request->validated();

        $rol->update(['name' => $validado['name']]);

        // syncPermissions se encarga de agregar los nuevos y quitar los que falten
        if (isset($validado['permisos'])) {
            $rol->syncPermissions($validado['permisos']);
        } else {
            // Si pasan el array vacío o no lo envían, limpia todos los permisos del rol
            $rol->syncPermissions([]);
        }

        return response()->json([
            'mensaje' => 'Rol actualizado con éxito',
            'data' => $rol->load('permissions')
        ], 200);
    }

    public function eliminar($id): JsonResponse
    {
        $rol = Role::findOrFail($id);

        if ($rol->name === 'administrador') {
            return response()->json(['mensaje' => 'El rol administrador no puede eliminarse.'], 422);
        }

        // Spatie remueve automáticamente las relaciones en las tablas intermedias al eliminar
        $rol->delete();

        return response()->json(['mensaje' => 'Rol eliminado con éxito'], 200);
    }

    // Método extra útil para que tu Frontend (React) cargue la lista de checkboxes de permisos
    public function listarPermisos(): JsonResponse
    {
        return response()->json(Permission::all(), 200);
    }
}