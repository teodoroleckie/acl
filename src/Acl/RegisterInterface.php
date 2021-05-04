<?php

namespace Tleckie\Acl\Acl;

use Tleckie\Acl\Resource\ResourceInterface;
use Tleckie\Acl\Role\RoleInterface;

interface RegisterInterface
{
    /**
     * @param RoleInterface|string $role
     * @return RegisterInterface
     */
    public function removeRole(RoleInterface|string $role): RegisterInterface;

    /**
     * @param ResourceInterface|string $resource
     * @return RegisterInterface
     */
    public function removeResource(ResourceInterface|string $resource): RegisterInterface;

    /**
     * @param RoleInterface|string $role
     * @param string[]             $parents
     * @return RegisterInterface
     */
    public function addRole(RoleInterface|string $role, array $parents = []): RegisterInterface;

    /**
     * @return RegisterInterface
     */
    public function removeAllRole(): RegisterInterface;

    /**
     * @return RegisterInterface
     */
    public function removeAllResource(): RegisterInterface;

    /**
     * @return array
     */
    public function rules(): array;

    /**
     * @param string      $role
     * @param string      $resource
     * @param string|null $privilege
     * @return bool
     */
    public function isAllowed(
        string $role,
        string $resource,
        string $privilege = null
    ): bool;

    /**
     * @param OperationEnum       $operation
     * @param PermissionTypeEnum  $type
     * @param RoleInterface[]     $roles
     * @param ResourceInterface[] $resources
     * @param array               $privileges
     * @return RegisterInterface
     */
    public function setRule(
        OperationEnum $operation,
        PermissionTypeEnum $type,
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): RegisterInterface;
}
