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

    /**
     * Builds the list of permissions by combining base permissions with special permissions.
     *
     */
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

    /**
     * Gets the list of filtered permissions.
     *
     */
    public function get(): array
    {
        return $this->filteredPermissions;
    }

    /**
     * Removes specific permissions from the current list of permissions.
     *
     * @param array $remove List of permissions to remove.
     */
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

    /**
     * Filters the current permissions to include only the specified ones.
     *
     * @param array $only List of permissions to retain.
     */
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
}
