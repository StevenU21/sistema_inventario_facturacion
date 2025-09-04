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
            'Municipality' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
            ],
            'Department' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
            ],
            'PaymentMethod' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
            ],
            'Tax' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
            ],
            'Entity' => [
                'first_name' => 'Nombre',
                'last_name' => 'Apellido',
                'identity_card' => 'Cédula',
                'ruc' => 'RUC',
                'address' => 'Dirección',
                'description' => 'Descripción',
                'is_client' => 'Es Cliente',
                'is_supplier' => 'Es Proveedor',
                'is_active' => 'Es Activo',
                'municipality_id' => 'Municipio'
            ],
            'UnitMeasure' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
            ],
            // Traducción para Product
            'Product' => [
                'name' => 'Nombre',
                'description' => 'Descripción',
                'barcode' => 'Código de Barras',
                'image' => 'Imagen',
                'status' => 'Estado',
                'brand_id' => 'Marca',
                'category_id' => 'Categoría',
                'tax_id' => 'Impuesto',
                'unit_measure_id' => 'Unidad de Medida',
                'entity_id' => 'Entidad',
            ],
            // Traducción para Inventory
            'Inventory' => [
                'stock' => 'Existencia',
                'min_stock' => 'Stock Mínimo',
                'purchase_price' => 'Precio de Compra',
                'sale_price' => 'Precio de Venta',
                'product_id' => 'Producto',
                'warehouse_id' => 'Almacén',
            ],
            // Traducción para InventoryMovement
            'InventoryMovement' => [
                'type' => 'Tipo',
                'adjustment_reason' => 'Motivo de Ajuste',
                'quantity' => 'Cantidad',
                'unit_price' => 'Precio Unitario',
                'total_price' => 'Precio Total',
                'reference' => 'Referencia',
                'notes' => 'Notas',
                'user_id' => 'Usuario',
                'inventory_id' => 'Inventario',
            ],
            'Warehouse' => [
                'name' => 'Nombre',
                'address' => 'Dirección',
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
            'Category' => 'Categoría',
            'Municipality' => 'Municipio',
            'Department' => 'Departamento',
            'PaymentMethod' => 'Método de Pago',
            'Tax' => 'Impuesto',
            'Entity' => 'Cliente & Proveedor',
            'UnitMeasure' => 'Unidad de Medida',
            'Product' => 'Producto',
            'Inventory' => 'Inventario',
            'InventoryMovement' => 'Movimiento de Inventario',
            'Warehouse' => 'Almacén',
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

        if ($field === 'is_client' || $field === 'Activo') {
            if ($value === 1 || $value === true || $value === '1') {
                return 'Verdadero';
            } elseif ($value === 0 || $value === false || $value === '0') {
                return 'Falso';
            }
        }

        if ($field === 'is_supplier' || $field === 'Activo') {
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
