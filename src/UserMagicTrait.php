<?php

namespace iMemento\SDK\Auth;

use ErrorException;

trait UserMagicTrait
{
    /**
     * Creates the permissions attribute
     *
     * @param array $permissions
     * @param array $roles
     * @return $this
     */
    public function createPermissions(?array $permissions, ?array $roles)
    {
        $this->permissions = [];

        if (! empty($roles)) {
            foreach($roles as $role) {
                try {
                    $this->permissions = array_merge($this->permissions, $permissions[$role]);
                } catch (ErrorException $e) {
                    \Log::warning("There is no role [$role] in " . config('app.name'));
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles ?? [];
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions ?? [];
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }

    /**
     * @param $resource
     * @return bool
     */
    public function owns($resource): bool
    {
        return $this->id === data_get($resource, 'user_id');
    }

}
