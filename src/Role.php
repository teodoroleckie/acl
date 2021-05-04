<?php

namespace Tleckie\Acl;

use Tleckie\Acl\Role\RoleInterface;

/**
 * Class Role
 *
 * @package Tleckie\Acl
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class Role implements RoleInterface
{
    /** @var string */
    private string $id;

    /** @var RoleInterface[] */
    private array $parents;

    /** @var RoleInterface[] */
    private array $children;

    /**
     * Role constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->parents = [];
        $this->children = [];
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function addParent(RoleInterface $role): RoleInterface
    {
        $this->parents[(string)$role] = $role;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeParent(RoleInterface $role): RoleInterface
    {
        unset($this->parents[(string)$role]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function parents(): array
    {
        return $this->parents;
    }

    /**
     * @inheritdoc
     */
    public function hasParents(): bool
    {
        return !empty($this->parents);
    }

    /**
     * @inheritdoc
     */
    public function addChildren(RoleInterface $role): RoleInterface
    {
        $this->children[(string)$role] = $role;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeChildren(RoleInterface $role): RoleInterface
    {
        unset($this->children[(string)$role]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function children(): array
    {
        return $this->children;
    }
}
