<?php

namespace Tleckie\Acl\Acl;

use Tleckie\Enum\Enum;

/**
 * Class OperationEnum
 *
 * @package Tleckie\Acl\Acl
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class OperationEnum extends Enum
{
    /** @var string */
    public const ADD = 'ADD';

    /** @var string */
    public const REMOVE = 'REMOVE';
}
