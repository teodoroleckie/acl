<?php

namespace Tleckie\Acl\Resource;

/**
 * Interface ResourceRecorderInterface
 *
 * @package Tleckie\Acl\Resource
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
interface ResourceRecorderInterface
{
    /**
     * @param string|ResourceInterface $resource
     * @param array                    $parents
     * @return $this
     */
    public function addResource(string|ResourceInterface $resource, array $parents = []): ResourceRecorderInterface;

    /**
     * @param ResourceInterface|string $resource
     * @return ResourceInterface
     */
    public function getResource(ResourceInterface|string $resource): ResourceInterface;

    /**
     * @param ResourceInterface|string $resource
     * @return bool
     */
    public function hasResource(ResourceInterface|string $resource): bool;

    /**
     * @param ResourceInterface|string $resource
     * @return ResourceRecorderInterface
     */
    public function removeResource(ResourceInterface|string $resource): ResourceRecorderInterface;

    /**
     * @return ResourceRecorderInterface
     */
    public function removeAllResource(): ResourceRecorderInterface;

    /**
     * @return array
     */
    public function resources(): array;
}
