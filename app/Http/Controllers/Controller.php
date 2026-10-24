<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// La documentación OpenAPI general está en app/Swagger/OpenApi.php
abstract class Controller
{
    /**
     * Devuelve la lista completa (como siempre) o paginada si se envía ?por_pagina=N (máx. 100).
     * La respuesta paginada trae los registros en "data" junto a current_page, last_page, total, etc.
     */
    protected function responderLista(Builder $query, Request $request): JsonResponse
    {
        if (! $request->filled('por_pagina')) {
            return response()->json($query->get(), 200);
        }

        // Sin un orden fijo las páginas podrían repetir u omitir registros
        if (empty($query->getQuery()->orders)) {
            $query->orderBy($query->getModel()->getQualifiedKeyName());
        }

        $porPagina = min(max((int) $request->input('por_pagina'), 1), 100);

        return response()->json($query->paginate($porPagina)->withQueryString(), 200);
    }
}
