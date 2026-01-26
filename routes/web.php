<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// // 1. Rutas totalmente públicas (Login y Registro)
// Route::get('login', [AuthController::class, 'showLogin'])->name('login');
// Route::post('login', [AuthController::class, 'login'])->name('login.post');

// // Permitimos crear usuarios sin estar logueado
// Route::get('users/create', [UserController::class, 'create'])->name('users.create');
// Route::post('users', [UserController::class, 'store'])->name('users.store');



// // 2. Ruta protegida (Solo logueados)
// Route::middleware(['auth'])->group(function () {
//     Route::post('logout', [AuthController::class, 'logout'])->name('logout');

//     // ESTA RUTA ES PARA TODOS LOS LOGUEADOS (Admin y User)
//     // Así el usuario normal puede entrar al listado sin el error 403
//     Route::get('users', [UserController::class, 'index'])->name('users.index');
//     Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

//     // Solo el admin puede gestionar usuarios
//     // En AppServiceProvider.php definimos 'admin-access'
//     Route::middleware(['can:admin-access'])->group(function (){
//         Route::resource('users', UserController::class)->except(['create', 'store', 'index', 'show']);
//     });
// });


