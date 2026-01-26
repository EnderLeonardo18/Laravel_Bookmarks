<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Muestra el formulario de login
    // ELIMINADO: En una API, Angular maneja sus propias vistas/formularios
    // public function showLogin(){
    //     return view('auth.login');
    // }

    // Procesa el intento de acceso
    public function login(Request $request){
        // Validamos que lleguen los datos
        $credentials = $request->validate([
            'email' => ['required' , 'email'],
            'password' => ['required'],
        ]);

        // Auth::attempt busca al usuario por email y compara la contraseña (usando Hash check)
        // Intentamos autenticar
        if(Auth::attempt($credentials)){
            // ELIMINADO: Las APIs no usan regeneración de sesión por cookies
            // $request->session()->regenerate();
            // return redirect()->intended('users'); // Redirige al CRUD

            /** @var \App\Models\User $user **/
            $user = Auth::user();
            $token = $user->createToken('main')->plainTextToken;

            // CAMBIADO: Devolvemos JSON con el token en lugar de redirect()
            // return redirect()->intended('users');
            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }


        // CAMBIADO: Devolvemos error 401 en lugar de back()
        // return back()->withErrors([
        //     'email' => 'Credenciales incorrectas'
        // ])->onlyInput('email');

        return response()->json([
            'Message' => 'Credenciales incorrectas'
        ], 401);
    }


    public function logout(Request $request){

        // NUEVO: Revocamos (borramos) el token actual en la base de datos
        $request->user()->currentAccessToken()->delete();

        // ELIMINADO: Lógica de sesiones de navegador
        // Auth::logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();
        // return redirect()->route('login')->with('Success', 'Haz cerrado sesión correctamente');

        return response()->json([
            'Message' => 'Sesión cerrada correctamente'
        ]);
    }
}
