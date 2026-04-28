<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Solo pedimos la contraseña actual si el usuario está intentando cambiar
            // el email o la contraseña (acciones sensibles)
            'current_password' => [
                $this->filled('password') || $this->email !== $this->user->email ? 'required' : 'nullable',
                function ($attribute, $value, $fail) {
                    if ($value && !Hash::check($value, $this->user->password)) {
                        $fail('La contraseña actual no es correcta');
                    }
                }
            ],

            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            // El ID al final es para que no de error de "ya existe" al editar tu propio usuario
            'username'   => 'required|string|max:50|unique:users,username,' .$this->user->id,
            'email' => 'required|email|unique:users,email,'  .$this->user->id,
            'role'       => ['required', new Enum(UserRole::class)],
            'password' => 'nullable|min:8|confirmed', // Nullable para no obligar a cambiarla
        ];
    }
}
