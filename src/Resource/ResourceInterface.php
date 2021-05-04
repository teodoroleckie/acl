<?php

namespace Tleckie\Acl\Resource;

/**
 * Interface ResourceInterface
 *
 * @package Tleckie\Acl\Resource
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface ResourceInterface
{
    /**
     * @return string
     */
    public function __toString(): string;

    /**
     * @param ResourceInterface $resource
     * @return ResourceInterface
     */
    public function addParent(ResourceInterface $resource): ResourceInterface;

    /**
     * @param ResourceInterface $resource
     * @return ResourceInterface
     */
    public function removeParent(ResourceInterface $resource): ResourceInterface;

    /**
     * @return ResourceInterface[]
     */
    public function parents(): array;

    /**
     * @return bool
     */
    public function hasParents(): bool;

    /**
     * @param ResourceInterface $resource
     * @return ResourceInterface
     */
    public function addChildren(ResourceInterface $resource): ResourceInterface;

    /**
     * @param ResourceInterface $resource
     * @return ResourceInterface
     */
    public function removeChildren(ResourceInterface $resource): ResourceInterface;

    /**
     * @return ResourceInterface[]
     */
    public function children(): array;
}
