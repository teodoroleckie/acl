<?php

namespace Tleckie\Acl\Permissions;

use Tleckie\Acl\Acl\OperationEnum;
use Tleckie\Acl\Acl\PermissionTypeEnum;
use Tleckie\Acl\Acl\RulesInterface;
use Tleckie\Acl\Resource\ResourceInterface;
use Tleckie\Acl\Role\RoleInterface;

/**
 * Interface PermissionsInterface
 *
 * @package Tleckie\Acl\Permissions
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface PermissionsInterface
{
    /**
     * @param OperationEnum       $operation
     * @param PermissionTypeEnum  $type
     * @param RoleInterface[]     $roles
     * @param ResourceInterface[] $resources
     * @param string[]            $privileges
     * @return mixed
     */
    public function setRule(
        OperationEnum $operation,
        PermissionTypeEnum $type,
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): mixed;

    /**
     * @param RoleInterface[]     $roles
     * @param ResourceInterface[] $resources
     * @param string[]            $privileges
     * @return mixed
     */
    public function removeDeny(array $roles = [], array $resources = [], array $privileges = []): mixed;

    /**
     * @param RoleInterface[]     $roles
     * @param ResourceInterface[] $resources
     * @param string[]            $privileges
     * @return mixed
     */
    public function removeAllow(array $roles = [], array $resources = [], array $privileges = []): mixed;

    /**
     * @param RoleInterface[]     $roles
     * @param ResourceInterface[] $resources
     * @param string[]            $privileges
     * @return mixed
     */
    public function allow(
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): mixed;

    /**
     * @param RoleInterface[]     $roles
     * @param ResourceInterface[] $resources
     * @param string[]            $privileges
     * @return mixed
     */
    public function deny(
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): mixed;

    /**
     * @param RoleInterface|string|null     $role
     * @param ResourceInterface|string|null $resource
     * @param string|null                   $privilege
     * @return bool
     */
    public function isAllowed(
        RoleInterface|string $role = null,
        ResourceInterface|string $resource = null,
        string $privilege = null
    ): bool;
}
