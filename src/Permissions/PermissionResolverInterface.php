<?php

namespace Tleckie\Acl\Permissions;

/**
 * Interface PermissionResolverInterface
 *
 * @package Tleckie\Acl\Permissions
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface PermissionResolverInterface
{
    /**
     * @param array       $rules
     * @param string      $role
     * @param string      $resource
     * @param string|null $privilege
     * @return bool
     */
    public function isAllowed(
        array &$rules,
        string $role,
        string $resource,
        string $privilege = null
    ): bool;
}
