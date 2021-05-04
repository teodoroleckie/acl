<?php

namespace Tleckie\Acl\Acl;

use Tleckie\Enum\Enum;

/**
 * Class PermissionTypeEnum
 *
 * @package Tleckie\Acl\Acl
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class PermissionTypeEnum extends Enum
{
    /** @var string */
    public const ALLOW = 'ALLOW';

    /** @var string */
    public const DENY = 'DENY';
}
