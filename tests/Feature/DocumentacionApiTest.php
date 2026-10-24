<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use OpenApi\Generator;
use Tests\TestCase;

class DocumentacionApiTest extends TestCase
{
    public function test_todas_las_rutas_de_la_api_estan_documentadas(): void
    {
        if (! defined('L5_SWAGGER_CONST_HOST')) {
            define('L5_SWAGGER_CONST_HOST', 'http://localhost:8000');
        }

        $documentadas = [];
        $spec = json_decode((new Generator)->generate([app_path()])->toJson(), true);
        foreach ($spec['paths'] as $ruta => $metodos) {
            foreach (array_keys($metodos) as $metodo) {
                $documentadas[] = strtoupper($metodo).' '.$ruta;
            }
        }

        $rutas = collect(Route::getRoutes())
            ->filter(fn ($r) => str_starts_with($r->uri(), 'api/')
                && ! str_starts_with($r->uri(), 'api/documentation')
                && ! str_starts_with($r->uri(), 'api/oauth2'))
            ->map(fn ($r) => collect($r->methods())->reject(fn ($m) => $m === 'HEAD')->first().' /'.$r->uri())
            ->values();

        $faltantes = $rutas->diff($documentadas)->values()->all();

        $this->assertSame([], $faltantes, 'Rutas sin documentar en app/Swagger/Rutas: '.implode(', ', $faltantes));
    }
}
