<?php

namespace Tleckie\Acl\Operation;

use Tleckie\Acl\Acl\OperationEnum;
use Tleckie\Acl\Acl\PermissionTypeEnum;

/**
 * Interface OperationChainInterface
 *
 * @package Tleckie\Acl\Operation
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface OperationChainInterface
{
    /**
     * @param OperationChainInterface $handler
     * @return OperationChainInterface
     */
    public function next(OperationChainInterface $handler): OperationChainInterface;

    /**
     * @param OperationEnum      $operation
     * @param PermissionTypeEnum $type
     * @param array              $roles
     * @param array              $resources
     * @param array              $privileges
     * @param array              $rules
     * @return OperationChainInterface
     */
    public function handle(
        OperationEnum $operation,
        PermissionTypeEnum $type,
        array $roles,
        array $resources,
        array $privileges,
        array &$rules
    ): OperationChainInterface;
}
