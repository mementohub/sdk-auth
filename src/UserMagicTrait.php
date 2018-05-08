<?php

namespace iMemento\SDK\Auth;

use ErrorException;
use iMemento\Exceptions\MissingRoleException;

trait UserMagicTrait
{
    /**
     * Creates the permissions attribute
     *
     * @param array $permissions
     * @param array $roles
     * @return $this
     * @throws MissingRoleException
     */
    public function createPermissions(array $permissions, array $roles)
    {
        $this->permissions = [];

        foreach($roles as $role) {
            try {
                $this->permissions = array_merge($this->permissions, $permissions[$role]);
            } catch (ErrorException $e) {
                \Log::error("There is no role [$role] in " . config('app.name'));
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