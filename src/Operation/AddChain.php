<?php

namespace Tleckie\Acl\Operation;

use Tleckie\Acl\Acl\OperationEnum;
use Tleckie\Acl\Acl\PermissionTypeEnum;
use Tleckie\Acl\Resource\ResourceInterface;

/**
 * Class AddChain
 *
 * @package Tleckie\Acl\Operation
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class AddChain implements OperationChainInterface
{
    /** @var OperationChainInterface|null */
    private OperationChainInterface|null $handler;

    /**
     * AddChain constructor.
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

        if ($operation === $operation::ADD()) {
            $this->handleRoles($roles, $resources, $rules, $type, $privileges);
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
     * @param array              $roles
     * @param array              $resources
     * @param array              $rules
     * @param PermissionTypeEnum $type
     * @param array              $privileges
     */
    private function handleRoles(
        array $roles,
        array $resources,
        array &$rules,
        PermissionTypeEnum $type,
        array $privileges
    ): void {

        foreach ($roles as $role) {
            $this->handleResources($resources, $rules, $role, $type, $privileges);
        }
    }

    /**
     * @param array              $resources
     * @param array              $rules
     * @param string             $role
     * @param PermissionTypeEnum $type
     * @param array              $privileges
     */
    private function handleResources(
        array $resources,
        array &$rules,
        string $role,
        PermissionTypeEnum $type,
        array $privileges
    ): void {

        $typeId = (string)$type;
        $items = &$rules['roles'];

        foreach ($resources as $resource) {
            $resourceId = (string)$resource;

            if (!isset($items[$role][$typeId][$resourceId])) {
                $items[$role][$typeId][$resourceId] = [];
            }

            $this->handleAllow($type, $resource, $rules, $role, $privileges, $privilege);

            foreach ($privileges as $privilege) {
                $items[$role][$typeId][$resourceId][$privilege] = $typeId;
            }
        }
    }

    /**
     * @param PermissionTypeEnum $type
     * @param ResourceInterface  $resource
     * @param array              $rules
     * @param string             $role
     * @param array              $privileges
     * @param                    $privilege
     */
    private function handleAllow(
        PermissionTypeEnum $type,
        ResourceInterface $resource,
        array &$rules,
        string $role,
        array $privileges,
        &$privilege
    ): void {

        $typeId = (string)$type;
        $items = &$rules['roles'];

        foreach ($resource->children() as $res) {
            if (!isset($items[$role][$typeId][(string)$res])) {
                $items[$role][$typeId][(string)$res] = [];
            }
            foreach ($privileges as $privilege) {
                $items[$role][$typeId][(string)$res][$privilege] = $typeId;
            }
            $this->handleAllow($type, $res, $rules, $role, $privileges, $privilege);
        }
    }
}
