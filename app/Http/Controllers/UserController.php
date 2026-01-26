<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    // CAMBIADO: Retornamos JSON con los usuarios
    public function index()
    {
        // Usamos paginación, que es mejor que User::all() para muchos datos
        $users = User::latest()->paginate(10);
        // return view('users.index', compact('users'));
        return response()->json($users);
    }


    // ELIMINADO: Angular tiene su propio formulario de creación
    // public function create(): View
    // {
    //     return view('users.create');
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        // Validamos los datos
        $data = $request->validated();
        // Encriptamos la contraseña manualmente
        $data['password'] = Hash::make($data['password']);

        // Los datos ya vienen validados aquí
        // User::create($data);
        // return redirect()->route('users.index');

        $user = User::create($data);
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // return view('users.show', compact('user'));
        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // return view('users.edit', compact('user'));
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // COMENTARIO: Si no se envió contraseña nueva, la quitamos del array
        // para que no sobrescriba la actual en la base de datos.
        if(empty($data['password'])){
            unset($data['password']);
            unset($data['password_confirmation']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        // Quitamos current_password porque no existe esa columna en la tabla users
        unset($data['current_password']);

        $user->update($data);
        // return redirect()->route('users.index')->with('Success', 'Usuario actualizado');

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        // return redirect()->route('users.index')->with('Success', 'Usuario eliminado');

        return response()->json([
            'Message' => 'Usuario eliminado'
        ]);
    }
}
