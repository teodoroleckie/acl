<?php

namespace Tleckie\Acl\Role;

use InvalidArgumentException;

/**
 * Class RoleRecorder
 *
 * @package Tleckie\Acl\Role
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class RoleRecorder implements RoleRecorderInterface
{
    /** @var array */
    private array $roles = [];

    /** @var RoleFactoryInterface */
    private RoleFactoryInterface $roleFactory;

    /**
     * RoleRecorder constructor.
     *
     * @param RoleFactoryInterface $roleFactory
     */
    public function __construct(RoleFactoryInterface $roleFactory)
    {
        $this->roleFactory = $roleFactory;
    }

    /**
     * @inheritdoc
     */
    public function addRole(RoleInterface|string $role, array $parents = []): RoleRecorderInterface
    {
        if ($this->hasRole($role)) {
            throw new InvalidArgumentException(
                sprintf('Role [%s] already exists', $role)
            );
        }

        $role = $this->roleFactory->create($role);

        $this->roles[(string)$role] = $role;

        foreach ($parents as $parent) {
            if (!$this->hasRole($parent)) {
                $this->addRole($parent);
            }

            $parent = $this->getRole($parent);

            $parent->addChildren($role);

            $role->addParent($parent);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasRole(RoleInterface|string $role): bool
    {
        return isset($this->roles[(string)$this->roleFactory->create($role)]);
    }

    /**
     * @inheritdoc
     */
    public function getRole(RoleInterface|string $role): RoleInterface
    {
        if (!$this->hasRole($role)) {
            throw new InvalidArgumentException(
                sprintf('Role [%s] not found', $role)
            );
        }

        return $this->roles[(string)$role];
    }

    /**
     * @inheritdoc
     */
    public function removeRole(RoleInterface|string $role): RoleRecorderInterface
    {
        try {
            $role = $this->getRole($role);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                sprintf('Role [%s] not found, can not remove', $role),
                $exception->getCode(),
                $exception
            );
        }

        foreach ($this->roles as $itemRole) {
            if ($role === $itemRole) {
                unset($this->roles[(string)$role]);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeAllRole(): RoleRecorderInterface
    {
        $this->roles = [];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function roles(): array
    {
        return $this->roles;
    }
}
