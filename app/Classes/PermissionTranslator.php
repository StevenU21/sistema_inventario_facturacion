<?php

namespace App\Classes;

class PermissionTranslator
{
    /**
     * Diccionario de traducciones de permisos.
     * Puedes agregar más traducciones según tus necesidades.
     */
    protected static array $translations = [
        // Acciones comunes
        'read' => 'Ver',
        'create' => 'Crear',
        'update' => 'Editar',
        'destroy' => 'Eliminar',
        // Sinónimos frecuentes de acciones
        'view' => 'Ver',
        'show' => 'Ver',
        'list' => 'Listar',
        'edit' => 'Editar',
        'delete' => 'Eliminar',
        'remove' => 'Eliminar',
        'store' => 'Crear',
        'manage' => 'Administrar',
        // Recursos
        'user' => 'usuario',
        'users' => 'usuarios',
        'permission' => 'permiso',
        'permissions' => 'permisos',
        'audits' => 'auditorías',
        'audit' => 'auditoría',
        'brands' => 'marcas',
        'brand' => 'marca',
        'categories' => 'categorías',
        'category' => 'categoría',
        'backups' => 'respaldos',
        'backup' => 'respaldo',
        'companies' => 'empresas',
        'company' => 'empresa',
        'unit_measures' => 'unidades de medida',
        'unit_measure' => 'unidad de medida',
        'departments' => 'departamentos',
        'department' => 'departamento',
        'municipalities' => 'municipios',
        'municipality' => 'municipio',
        'payment_methods' => 'métodos de pago',
        'payment_method' => 'método de pago',
        'taxes' => 'impuestos',
        'tax' => 'impuesto',
        'entities' => 'entidades',
        'entity' => 'entidad',
        'products' => 'productos',
        'product' => 'producto',
        'roles' => 'roles',
        'role' => 'rol',
        'warehouses' => 'almacenes',
        'warehouse' => 'almacén',
        'inventories' => 'inventarios',
        'inventory' => 'inventario',
        'inventory_movements' => 'movimientos de inventario',
        'inventory_movement' => 'movimiento de inventario',
        'sizes' => 'tallas',
        'size' => 'talla',
        'colors' => 'colores',
        'color' => 'color',
        'purchases' => 'compras',
        'purchase' => 'compra',
        'kardex' => 'kardex',
        'kardexes' => 'kardexes',
        // Permisos especiales (ejemplo)
        'assign permissions' => 'Asignar permisos',
        'revoke permissions' => 'Revocar permisos',
        'export products' => 'Exportar productos',
        'reactivate users' => 'Reactivar usuarios',
        'export users' => 'Exportar usuarios',
        'export audits' => 'Exportar auditorías',
        'read suppliers' => 'Ver proveedores',
        'create suppliers' => 'Crear proveedores',
        'update suppliers' => 'Editar proveedores',
        'read clients' => 'Ver clientes',
        'create clients' => 'Crear clientes',
        'update clients' => 'Editar clientes',
        'export clients' => 'Exportar clientes',
        'export suppliers' => 'Exportar proveedores',
        'export inventory_movements' => 'Exportar movimientos de inventario',
        'export inventories' => 'Exportar inventarios',
        'export kardex' => 'Exportar kardex',
    ];

    /**
     * Traduce un permiso al español.
     */
    public static function translate(string $permission): string
    {
        // Normaliza: minúsculas y separadores comunes a espacios
        $original = $permission;
        $normalized = strtolower(trim($permission));
        $normalized = str_replace(['.', '-', ':', '/'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        // Coincidencia directa
        if (isset(self::$translations[$normalized])) {
            return self::$translations[$normalized];
        }

        // Detectar patrón compuesto. Intentar diferentes órdenes: "action resource" y "resource action"
        $parts = explode(' ', $normalized);
        if (count($parts) >= 2) {
            // Caso A: action resource(+ ...)
            $actionKey = $parts[0];
            $resourceKey = implode(' ', array_slice($parts, 1));
            $action = self::$translations[$actionKey] ?? $actionKey;
            $resource = self::$translations[$resourceKey] ?? self::translateResourceTokens($resourceKey);
            if ($action !== $actionKey || $resource !== $resourceKey) {
                return trim($action . ' ' . $resource);
            }

            // Caso B: resource action (e.g., "users view")
            $maybeResourceKey = implode(' ', array_slice($parts, 0, -1));
            $maybeActionKey = end($parts);
            $action = self::$translations[$maybeActionKey] ?? $maybeActionKey;
            $resource = self::$translations[$maybeResourceKey] ?? self::translateResourceTokens($maybeResourceKey);
            if ($action !== $maybeActionKey || $resource !== $maybeResourceKey) {
                return trim($action . ' ' . $resource);
            }
        }

        // Si no se encuentra traducción, retorna el permiso original
        return $original;
    }

    /**
     * Traduce recursos compuestos por tokens (e.g., "inventory movements", "unit measures").
     */
    protected static function translateResourceTokens(string $resource): string
    {
        $tokens = preg_split('/[ _]+/', $resource);
        $translatedTokens = [];
        foreach ($tokens as $token) {
            $translatedTokens[] = self::$translations[$token] ?? $token;
        }
        return implode(' ', $translatedTokens);
    }

    /**
     * Traduce una colección de permisos.
     * @param iterable $permissions
     * @return array
     */
    public static function translateMany(iterable $permissions): array
    {
        $translated = [];
        foreach ($permissions as $permission) {
            $translated[$permission] = self::translate($permission);
        }
        return $translated;
    }
}
