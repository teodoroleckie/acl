<?php

namespace Tleckie\Acl;

use Tleckie\Acl\Resource\ResourceInterface;

/**
 * Class Resource
 *
 * @package Tleckie\Acl
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class Resource implements ResourceInterface
{
    /** @var string */
    private string $id;

    /** @var ResourceInterface[] */
    private array $parents;

    /** @var ResourceInterface[] */
    private array $children;

    /**
     * Resource constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
        $this->parents = [];
        $this->children = [];
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function addParent(ResourceInterface $resource): ResourceInterface
    {
        $this->parents[(string)$resource] = $resource;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeParent(ResourceInterface $resource): ResourceInterface
    {
        unset($this->parents[(string)$resource]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function parents(): array
    {
        return $this->parents;
    }

    /**
     * @inheritdoc
     */
    public function hasParents(): bool
    {
        return !empty($this->parents);
    }

    /**
     * @inheritdoc
     */
    public function addChildren(ResourceInterface $resource): ResourceInterface
    {
        $this->children[(string)$resource] = $resource;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeChildren(ResourceInterface $resource): ResourceInterface
    {
        unset($this->children[(string)$resource]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function children(): array
    {
        return $this->children;
    }
}
