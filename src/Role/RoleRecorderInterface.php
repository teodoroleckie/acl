<?php

namespace Tleckie\Acl\Role;

/**
 * Interface RoleRecorderInterface
 *
 * @package Tleckie\Acl\Role
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface RoleRecorderInterface
{
    /**
     * @param RoleInterface|string $role
     * @param RoleInterface[]      $parents
     * @return RoleRecorderInterface
     */
    public function addRole(RoleInterface|string $role, array $parents = []): RoleRecorderInterface;

    /**
     * @param RoleInterface|string $role
     * @return RoleInterface
     */
    public function getRole(RoleInterface|string $role): RoleInterface;

    /**
     * @param RoleInterface|string $role
     * @return bool
     */
    public function hasRole(RoleInterface|string $role): bool;

    /**
     * @param RoleInterface|string $role
     * @return RoleRecorderInterface
     */
    public function removeRole(RoleInterface|string $role): RoleRecorderInterface;

    /**
     * @return RoleRecorderInterface
     */
    public function removeAllRole(): RoleRecorderInterface;

    /**
     * @return array
     */
    public function roles(): array;
}
