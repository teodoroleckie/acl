<?php

namespace Tleckie\Acl\Acl;

use Tleckie\Acl\Operation\AddChain;
use Tleckie\Acl\Operation\OperationChainInterface;
use Tleckie\Acl\Operation\RemoveChain;
use Tleckie\Acl\Permissions\AllowRule;
use Tleckie\Acl\Permissions\DenyRule;
use Tleckie\Acl\Permissions\PermissionResolver;
use Tleckie\Acl\Permissions\Resolver;
use Tleckie\Acl\Resource\ResourceInterface;
use Tleckie\Acl\Role\RoleInterface;
use Tleckie\Acl\Role\RoleRecorderInterface;

/**
 * Class Register
 *
 * @package Tleckie\Acl\Acl
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class Register implements RegisterInterface
{
    /** @var array */
    private array $rules;

    /** @var RoleRecorderInterface */
    private RoleRecorderInterface $roleRecorder;

    /** @var OperationChainInterface */
    private OperationChainInterface $operations;

    /** @var PermissionResolver */
    private PermissionResolver $resolver;

    /**
     * Register constructor.
     *
     * @param RoleRecorderInterface $roleRecorder
     */
    public function __construct(RoleRecorderInterface $roleRecorder)
    {
        $this->roleRecorder = $roleRecorder;

        $this->operations = (new RemoveChain(new AddChain()));

        $this->resolver = new Resolver();

        $this->rules = [];
    }

    /**
     * @inheritdoc
     */
    public function removeRole(RoleInterface|string $role): RegisterInterface
    {
        foreach ($this->rules['roles'] ?? [] as $roleName => $item) {
            foreach ($item['parents'] ?? [] as $parentName => $parent) {
                if ($parentName === (string)$role) {
                    unset($this->rules['roles'][$roleName]['parents'][$parentName]);
                }
            }
        }

        unset($this->rules['roles'][(string)$role]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeResource(ResourceInterface|string $resource): RegisterInterface
    {
        foreach ($this->rules['roles'] ?? [] as $roleName => $item) {
            foreach ([(string)PermissionTypeEnum::DENY(), (string)PermissionTypeEnum::ALLOW()] as $type) {
                if (isset($this->rules['roles'][$roleName][$type][(string)$resource])) {
                    unset($this->rules['roles'][$roleName][$type][(string)$resource]);
                }
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addRole(
        RoleInterface|string $role,
        array $parents = []
    ): RegisterInterface {
        $role = $this->roleRecorder->getRole($role);

        $this->initializeRule('roles', $role);

        foreach ($parents as $parent) {
            $this->rules['roles'][(string)$role]['parents'][(string)$parent] = [];
        }

        return $this;
    }

    /**
     * @param string                          $type
     * @param RoleInterface|ResourceInterface $item
     * @return RegisterInterface
     */
    private function initializeRule(
        string $type,
        RoleInterface|ResourceInterface $item
    ): RegisterInterface {
        $this->rules[$type][(string)$item]['parents'] = [];
        $this->rules[$type][(string)$item][(string)PermissionTypeEnum::DENY()] = [];
        $this->rules[$type][(string)$item][(string)PermissionTypeEnum::ALLOW()] = [];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeAllRole(): RegisterInterface
    {
        $this->rules = [];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeAllResource(): RegisterInterface
    {
        foreach ($this->rules['roles'] as $roleName => &$item) {
            foreach ([(string)PermissionTypeEnum::DENY(), (string)PermissionTypeEnum::ALLOW()] as $type) {
                $item[$type] = [];
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * @inheritdoc
     */
    public function isAllowed(
        string $role,
        string $resource,
        string $privilege = null
    ): bool {
        return $this->resolver->isAllowed(
            $this->rules,
            $role,
            $resource,
            $privilege
        );
    }

    /**
     * @inheritdoc
     */
    public function setRule(
        OperationEnum $operation,
        PermissionTypeEnum $type,
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): RegisterInterface {
        $this->operations->handle(
            $operation,
            $type,
            $roles,
            $resources,
            $privileges,
            $this->rules
        );

        return $this;
    }
}
