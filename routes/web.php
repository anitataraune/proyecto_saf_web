<?php

use Illuminate\Support\Facades\Route;

// Frontend React (compilado en public/index.html): cualquier ruta que no sea de la API la maneja React Router
Route::get('/{ruta?}', function () {
    $spa = public_path('index.html');

    return file_exists($spa) ? response()->file($spa) : view('welcome');
})->where('ruta', '^(?!api|docs|up|sanctum).*$');
