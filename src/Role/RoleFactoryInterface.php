<?php

namespace Tleckie\Acl\Role;

/**
 * Interface RoleFactoryInterface
 *
 * @package Tleckie\Acl\Role
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface RoleFactoryInterface
{
    /**
     * @param string|RoleInterface $resource
     * @return RoleInterface
     */
    public function create(string|RoleInterface $resource): RoleInterface;
}
