<?php

namespace Tleckie\Acl\Resource;

use InvalidArgumentException;

/**
 * Class ResourceRecorder
 *
 * @package Tleckie\Acl\Resource
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>ç
 */
class ResourceRecorder implements ResourceRecorderInterface
{
    /** @var ResourceFactoryInterface */
    private ResourceFactoryInterface $resourceFactory;

    /** @var array */
    private array $resources = [];

    /**
     * ResourceRecorder constructor.
     *
     * @param ResourceFactoryInterface $resourceFactory
     */
    public function __construct(ResourceFactoryInterface $resourceFactory)
    {
        $this->resourceFactory = $resourceFactory;
    }

    /**
     * @param string|ResourceInterface $resource
     * @param array                    $parents
     * @return ResourceRecorderInterface
     */
    public function addResource(
        string|ResourceInterface $resource,
        array $parents = []
    ): ResourceRecorderInterface {

        if ($this->hasResource($resource)) {
            throw new InvalidArgumentException(
                sprintf('Resource [%s] already exists', $resource)
            );
        }

        $resource = $this->resourceFactory->create($resource);

        $this->resources[(string)$resource] = $resource;

        foreach ($parents as $parent) {
            if (!$this->hasResource($parent)) {
                $this->addResource($parent);
            }

            $parent = $this->getResource($parent);

            $parent->addChildren($resource);

            $resource->addParent($parent);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasResource(ResourceInterface|string $resource): bool
    {
        return isset($this->resources[(string)$this->resourceFactory->create($resource)]);
    }

    /**
     * @inheritdoc
     */
    public function getResource(ResourceInterface|string $resource): ResourceInterface
    {
        if (!$this->hasResource($resource)) {
            throw new InvalidArgumentException(
                sprintf('Resource [%s] not found', $resource)
            );
        }

        return $this->resources[(string)$resource];
    }

    /**
     * @param ResourceInterface|string $resource
     * @return ResourceRecorderInterface
     */
    public function removeResource(ResourceInterface|string $resource): ResourceRecorderInterface
    {
        try {
            $resource = $this->getResource($resource);
        } catch (InvalidArgumentException $exception) {
            throw new InvalidArgumentException(
                sprintf('Resource [%s] not found, can not remove', $resource),
                $exception->getCode(),
                $exception
            );
        }

        foreach ($this->resources as $item) {
            if ($resource === $item) {
                unset($this->resources[(string)$resource]);
            }
        }

        return $this;
    }

    /**
     * @return ResourceRecorderInterface
     */
    public function removeAllResource(): ResourceRecorderInterface
    {
        $this->resources = [];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function resources(): array
    {
        return $this->resources;
    }
}
