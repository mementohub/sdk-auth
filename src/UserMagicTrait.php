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
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole(string $role)
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermission(string $permission)
    {
        return in_array($permission, $this->permissions);
    }

    /**
     * @param $resource
     * @return bool
     */
    public function owns(object $resource)
    {
        return $this->id === $resource->user_id;
    }

}