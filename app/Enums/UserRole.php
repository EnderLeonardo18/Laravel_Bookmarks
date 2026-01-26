<?php

namespace App\Enums;

enum UserRole: string {
    case ADMIN = 'admin';
    case USER = 'user';


    // Método para obtener etiquetas bonitas en la vista
    public function label() {
        return match($this){
            self::ADMIN => 'Administrador',
            self::USER => 'Usuario Normal',
        };
    }
}

