<?php

namespace Tleckie\Acl\Permissions;

use Tleckie\Acl\Acl\PermissionTypeEnum;

/**
 * Class Resolver
 *
 * @package Tleckie\Acl\Permissions
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class Resolver implements PermissionResolver
{
    /**
     * @inheritdoc
     */
    public function isAllowed(
        array &$rules,
        string $role,
        string $resource,
        string $privilege = null
    ): bool {
        return !$this->check(
            PermissionTypeEnum::DENY(),
            $rules,
            $role,
            $resource,
            $privilege
        )
            &&
            $this->check(
                PermissionTypeEnum::ALLOW(),
                $rules,
                $role,
                $resource,
                $privilege
            );
    }

    /**
     * @param string      $type
     * @param array       $rules
     * @param string      $role
     * @param string      $resource
     * @param string|null $privilege
     * @return bool
     */
    private function check(
        string $type,
        array &$rules,
        string $role,
        string $resource,
        string $privilege = null
    ): bool {
        $item = &$rules['roles'];
        if (isset($item[$role][$type][$resource][$privilege])) {
            return true;
        }

        foreach ($item[$role]['parents'] ?? [] as $parent => $value) {
            if ($this->check($type, $rules, $parent, $resource, $privilege)) {
                return true;
            }
        }

//        foreach ($item[$role]['parents'] ?? [] as $parent => $value) {
//            if ($this->check($type, $rules, $parent, $resource)) {
//                return true;
//            }
//        }

        if (isset($item[$role][$type][$resource]) &&
            !count($item[$role][$type][$resource])) {
            return true;
        }

        return false;
    }
}
