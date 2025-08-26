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
            'Brand' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
            ],
            'Company' => [
                'name' => 'Nombre',
                'ruc' => 'RUC',
                'logo' => 'Logo',
                'description' => 'Descripción',
                'address' => 'Dirección',
                'phone' => 'Teléfono',
                'email' => 'Correo',
            ],
            'Category' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
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
            'Brand' => 'Marca',
            'Company' => 'Empresa',
            'Category' => 'Categoría'
        ];
    }
    /**
     * Traduce valores estáticos de campos conocidos.
     */
    public static function translateValue(string $field, $value)
    {
        // Traducción para is_active
        if ($field === 'is_active' || $field === 'Activo') {
            if ($value === 1 || $value === true || $value === '1') {
                return 'Verdadero';
            } elseif ($value === 0 || $value === false || $value === '0') {
                return 'Falso';
            }
        }
        // Traducción para gender
        if ($field === 'gender' || $field === 'Género') {
            if ($value === 'male') {
                return 'Hombre';
            } elseif ($value === 'female') {
                return 'Mujer';
            }
        }
        return $value;
    }
}
