<?php

namespace App\Classes;

class PermissionManager
{
    /**
     * List of basic permissions assigned to resources.
     */
    private array $permissions;

    /**
     * List of permissions that have been filtered and organized.
     */
    private array $filteredPermissions;

    /**
     * Special permissions that override the default permissions.
     */
    private array $specialPermissions;

    /**
     * Default allowed actions on resources.
     */
    private const DEFAULT_ACTIONS = ['read', 'create', 'update', 'destroy'];

    /**
     * Definición (opcional) de roles a procesar.
     */
    private array $rolesDefinition = [];

    /**
     * Bandera para indicar si ya se hizo build de permisos (cache simple) cuando se usa la API estática.
     */
    private bool $built = false;

    /**
     * Class constructor.
     *
     * @param array $permissions
     * @param array $specialPermissions
     */
    public function __construct(array $permissions = [], array $specialPermissions = [])
    {
        $this->permissions = $permissions;
        $this->specialPermissions = $specialPermissions;
        $this->filteredPermissions = $this->buildPermissions();
    }

    public static function make(array $permissions = [], array $special = []): self
    {
        return new self($permissions, $special);
    }

    public function withRoles(array $rolesDefinition): self
    {
        $this->rolesDefinition = $rolesDefinition;
        return $this;
    }

    public function getRolesDefinition(): array
    {
        return $this->rolesDefinition;
    }

    private function buildPermissions(): array
    {
        $filtered = [];

        foreach ($this->permissions as $key => $value) {
            $resource = is_numeric($key) ? $value : $key;
            $actions = is_numeric($key) ? [] : $value;

            $actionsList = $actions ?: self::DEFAULT_ACTIONS;
            $basePermissions = array_map(
                fn($action) => sprintf('%s %s', $action, $resource),
                $actionsList
            );

            if (isset($this->specialPermissions[$resource])) {
                $specials = $this->specialPermissions[$resource];
                $filtered[$resource] = array_unique(array_merge($basePermissions, $specials));
            } else {
                $filtered[$resource] = $basePermissions;
            }
        }

        return $filtered;
    }

    public function ensureBuilt(): self
    {
        if (!$this->built) {
            $this->filteredPermissions = $this->buildPermissions();
            $this->built = true;
        }
        return $this;
    }

    public function get(): array
    {
        return $this->filteredPermissions;
    }

    public function remove(array $remove): self
    {
        $clone = clone $this;

        foreach ($remove as $r) {
            foreach ($clone->filteredPermissions as $resource => &$actions) {
                $actions = array_values(array_diff($actions, [$r]));
                if (empty($actions)) {
                    unset($clone->filteredPermissions[$resource]);
                }
            }
        }

        return $clone;
    }

    public function only(array $only): self
    {
        $clone = clone $this;

        foreach ($clone->filteredPermissions as $resource => &$actions) {
            $actions = array_values(array_intersect($actions, $only));
            if (empty($actions)) {
                unset($clone->filteredPermissions[$resource]);
            }
        }

        return $clone;
    }

    public function all(): array
    {
        if (empty($this->filteredPermissions)) {
            return [];
        }
        return array_values(array_unique(array_merge(...array_values($this->filteredPermissions))));
    }

    public function pick(array $permissionNames): array
    {
        $all = $this->all();
        $set = array_flip($all);
        $picked = [];
        foreach ($permissionNames as $p) {
            if (isset($set[$p])) {
                $picked[] = $p;
            }
        }
        return array_values(array_unique($picked));
    }

    public function roles(array $definitions): array
    {
        $allFlat = $this->all();
        $catalog = array_flip($allFlat);
        $result = [];

        foreach ($definitions as $role => $def) {
            // Asignar todos los permisos
            if ($def === '*' || (is_array($def) && count($def) === 1 && reset($def) === '*')) {
                $result[$role] = $allFlat;
                continue;
            }

            if (!is_array($def)) {
                // Permite pasar una cadena tipo "read users create users"
                $def = ['*' => $def];
            }

            $rolePerms = [];

            foreach ($def as $resource => $items) {
                // Caso: índice numérico => item es permiso completo
                if (is_int($resource)) {
                    $maybePermission = $items;
                    if (isset($catalog[$maybePermission])) {
                        $rolePerms[] = $maybePermission;
                    }
                    continue;
                }

                // Normalizar listado de acciones / permisos
                if (!is_array($items)) {
                    $items = preg_split('/[\s,|]+/', trim((string) $items)) ?: [];
                }

                foreach ($items as $item) {
                    if ($item === null || $item === '') {
                        continue;
                    }
                    // Si contiene un espacio presumimos que ya es un permiso completo
                    $permission = str_contains($item, ' ') ? $item : sprintf('%s %s', $item, $resource);
                    if (isset($catalog[$permission])) {
                        $rolePerms[] = $permission;
                    }
                }
            }

            $result[$role] = array_values(array_unique($rolePerms));
        }

        return $result;
    }

    public function sync(?array $rolesDefinition = null): array
    {
        // Evitar referencia directa a clases Spatie si no están cargadas (permite testear aislado).
        if (!class_exists(\Spatie\Permission\Models\Permission::class) || !class_exists(\Spatie\Permission\Models\Role::class)) {
            throw new \RuntimeException('Spatie Permission classes not found.');
        }

        if ($rolesDefinition !== null) {
            $this->rolesDefinition = $rolesDefinition;
        }

        $this->ensureBuilt();

        $all = $this->get();
        $flat = $this->all();

        $created = 0;
        $existing = 0;
        foreach ($flat as $permName) {
            $model = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permName]);
            $model->wasRecentlyCreated ? $created++ : $existing++;
        }

        $roles = $this->roles($this->rolesDefinition);
        $attached = [];
        foreach ($roles as $roleName => $permissions) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions); // sync para limpiar permisos previos obsoletos
            $attached[$roleName] = count($permissions);
        }

        return [
            'permissions_total' => count($flat),
            'permissions_created' => $created,
            'permissions_existing' => $existing,
            'roles_processed' => count($roles),
            'roles' => $attached,
        ];
    }
}
