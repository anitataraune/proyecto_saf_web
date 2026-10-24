<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualizarResponsableRequest;
use App\Http\Requests\GuardarResponsableRequest;
use App\Models\Responsable;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{
    public function listar(Request $request)
    {
        $query = Responsable::query();
        if ($request->boolean('incluir_bajas')) {
            $query->withTrashed();
        }

        return $this->responderLista($query, $request);
    }

    public function guardar(GuardarResponsableRequest $request)
    {
        $validado = $request->validated();

        $responsable = Responsable::create($validado);

        return response()->json(['mensaje' => 'Responsable registrado', 'data' => $responsable], 201);
    }

    public function mostrar($id)
    {
        $responsable = Responsable::withTrashed()->findOrFail($id);

        return response()->json($responsable, 200);
    }

    public function actualizar(ActualizarResponsableRequest $request, $id)
    {
        $responsable = Responsable::withTrashed()->findOrFail($id);

        $validado = $request->validated();

        $responsable->update($validado);

        return response()->json(['mensaje' => 'Responsable actualizado', 'data' => $responsable], 200);
    }

    public function eliminar($id)
    {
        $responsable = Responsable::findOrFail($id);

        if ($responsable->asignaciones()->where('estado_asignacion', 'asignado')->exists()) {
            return response()->json(['mensaje' => 'El responsable tiene activos asignados; registre primero su devolución.'], 422);
        }

        $responsable->delete();

        return response()->json(['mensaje' => 'Responsable enviado a papelera'], 200);
    }

    public function restaurar($id)
    {
        $responsable = Responsable::onlyTrashed()->findOrFail($id);
        $responsable->restore();

        return response()->json(['mensaje' => 'Responsable restaurado con éxito'], 200);
    }
}
