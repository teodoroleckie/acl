<?php

namespace Tleckie\Acl\Operation;

use Tleckie\Acl\Acl\OperationEnum;
use Tleckie\Acl\Acl\PermissionTypeEnum;

/**
 * Class RemoveChain
 *
 * @package Tleckie\Acl\Operation
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class RemoveChain implements OperationChainInterface
{
    /** @var OperationChainInterface|null */
    private OperationChainInterface|null $handler;

    /**
     * RemoveChain constructor.
     *
     * @param OperationChainInterface|null $handler
     */
    public function __construct(?OperationChainInterface $handler = null)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritdoc
     */
    public function next(OperationChainInterface $handler): OperationChainInterface
    {
        $this->handler = $handler;

        return $handler;
    }

    /**
     * @inheritdoc
     */
    public function handle(
        OperationEnum $operation,
        PermissionTypeEnum $type,
        array $roles,
        array $resources,
        array $privileges,
        array &$rules
    ): OperationChainInterface {
        if ($operation === $operation::REMOVE()) {
            foreach ($roles as $role) {
                $this->handleResources($resources, $privileges, $rules, $role, $type);
            }
        }

        if ($this->handler) {
            $this->handler->handle(
                $operation,
                $type,
                $roles,
                $resources,
                $privileges,
                $rules
            );
        }

        return $this;
    }

    /**
     * @param array  $resources
     * @param array  $privileges
     * @param array  $rules
     * @param string $roleId
     * @param string $typeId
     */
    private function handleResources(
        array $resources,
        array $privileges,
        array &$rules,
        string $roleId,
        string $typeId
    ): void {
        $items = &$rules['roles'];

        foreach ($resources as $resource) {
            $this->handlePrivileges($privileges, $rules, $roleId, $typeId, $resource);

            if (!count($privileges)) {
                unset($items[$roleId][$typeId][(string)$resource]);
            }
        }
    }

    /**
     * @param array  $privileges
     * @param array  $rules
     * @param string $roleId
     * @param string $typeId
     * @param string $resourceId
     */
    private function handlePrivileges(
        array $privileges,
        array &$rules,
        string $roleId,
        string $typeId,
        string $resourceId
    ): void {
        $items = &$rules['roles'];
        foreach ($privileges as $privilege) {
            unset($items[$roleId][$typeId][$resourceId][$privilege]);
        }

        if (isset($items[$roleId][$typeId][$resourceId]) && !count($items[$roleId][$typeId][$resourceId])) {
            unset($items[$roleId][$typeId][$resourceId]);
        }
    }
}
