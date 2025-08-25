<?php

namespace App\Classes;

class AuditTranslation
{
    /**
     * Traducción de atributos por modelo
     */
    public static function attributeTranslations(): array
    {
        return [
            'User' => [
                'first_name' => 'Nombre',
                'last_name' => 'Apellido',
                'email' => 'Correo',
                'password' => 'Contraseña',
                'is_active' => 'Activo',
            ],
            'Profile' => [
                'avatar' => 'Avatar',
                'phone' => 'Teléfono',
                'identity_card' => 'Cédula',
                'gender' => 'Género',
                'address' => 'Dirección',
                'user_id' => 'Usuario',
            ],
        ];
    }

    /**
     * Traducción de eventos
     */
    public static function eventMap(): array
    {
        return [
            'created' => 'Creado',
            'updated' => 'Actualizado',
            'deleted' => 'Eliminado',
        ];
    }

    /**
     * Traducción de modelos
     */
    public static function modelMap(): array
    {
        return [
            'User' => 'Usuario',
            'Profile' => 'Perfil',
        ];
    }
}
