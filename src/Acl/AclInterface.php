<?php

namespace Tleckie\Acl\Acl;

use Tleckie\Acl\Permissions\PermissionsInterface;
use Tleckie\Acl\Resource\ResourceRecorderInterface;
use Tleckie\Acl\Role\RoleRecorderInterface;

/**
 * Interface AclInterface
 *
 * @package Tleckie\Acl\Acl
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface AclInterface extends ResourceRecorderInterface, RoleRecorderInterface, PermissionsInterface
{
    /**
     * @return array
     */
    public function rules(): array;
}
