<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Sin Token)
|--------------------------------------------------------------------------
*/

// Login para obtener el token
Route::post('login', [AuthController::class, 'login']);

// Permitir el registro de usuarios desde el frontend sin estar logueado
Route::post('users', [UserController::class, 'store']);


/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Token de Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Cerrar sesión (revoca el token)
    Route::post('logout', [AuthController::class, 'logout']);

    // Obtener los datos del usuario autenticado (ya viene por defecto)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // CRUD de Usuarios
    // Usamos apiResource porque no necesitamos las rutas 'create' ni 'edit' (que devuelven formularios)
    // Ya definimos 'store' como pública arriba, así que la exceptuamos aquí
    Route::apiResource('users', UserController::class)->except(['store']);

    // 2. RUTA DE BOOKMARKS
    Route::get('admin/bookmarks', [BookmarkController::class, 'allBookmarksAdmin']);
    // Usamos apiResource para tener index, store, show, update y destroy en una sola línea
    Route::apiResource('bookmarks', BookmarkController::class);

});
