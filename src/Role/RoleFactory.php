<?php

namespace Tleckie\Acl\Role;

use Tleckie\Acl\Role;

/**
 * Class RoleFactory
 *
 * @package Tleckie\Acl\Role
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class RoleFactory implements RoleFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function create(RoleInterface|string $role): RoleInterface
    {
        if ($role instanceof RoleInterface) {
            return $role;
        }

        return new Role($role);
    }
}
