<?php

namespace Tleckie\Acl\Resource;

use Tleckie\Acl\Resource;

/**
 * Class ResourceFactory
 *
 * @package Tleckie\Acl\Resource
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class ResourceFactory implements ResourceFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function create(ResourceInterface|string $resource): ResourceInterface
    {
        if ($resource instanceof ResourceInterface) {
            return $resource;
        }

        return new Resource($resource);
    }
}
