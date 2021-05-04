<?php

namespace Tleckie\Acl\Resource;

/**
 * Interface ResourceFactoryInterface
 *
 * @package Tleckie\Acl\Resource
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface ResourceFactoryInterface
{
    /**
     * @param string|ResourceInterface $resource
     * @return ResourceInterface
     */
    public function create(string|ResourceInterface $resource): ResourceInterface;
}
