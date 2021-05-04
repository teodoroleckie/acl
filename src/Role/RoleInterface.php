<?php

namespace Tleckie\Acl\Role;

/**
 * Interface RoleInterface
 *
 * @package Tleckie\Acl\Role
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface RoleInterface
{
    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @param RoleInterface $role
     * @return RoleInterface
     */
    public function addParent(RoleInterface $role): RoleInterface;

    /**
     * @param RoleInterface $role
     * @return RoleInterface
     */
    public function removeParent(RoleInterface $role): RoleInterface;

    /**
     * @return RoleInterface[]
     */
    public function parents(): array;

    /**
     * @return bool
     */
    public function hasParents(): bool;

    /**
     * @param RoleInterface $role
     * @return RoleInterface
     */
    public function addChildren(RoleInterface $role): RoleInterface;

    /**
     * @param RoleInterface $role
     * @return RoleInterface
     */
    public function removeChildren(RoleInterface $role): RoleInterface;

    /**
     * @return RoleInterface[]
     */
    public function children(): array;
}
