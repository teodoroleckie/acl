<?php

namespace Tleckie\Acl\Tests\Resources;

use PHPUnit\Framework\TestCase;
use Tleckie\Acl\Resource;
use Tleckie\Acl\Resource\ResourceFactory;
use Tleckie\Acl\Resource\ResourceRecorder;
use Tleckie\Acl\Resource\ResourceInterface;

/**
 * Class ResourceRecorderTest
 *
 * @package Tleckie\Acl\Tests\Resources
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class ResourceRecorderTest extends TestCase
{
    /** @var ResourceRecorder */
    private ResourceRecorder $recorder;

    public function setUp(): void
    {
        $this->recorder = new ResourceRecorder(new ResourceFactory());
    }

    /**
     * @test
     */
    public function addResource(): void
    {
        $resource = new Resource('test');
        $this->recorder->addResource($resource, ['parent']);
        static::assertTrue($resource->hasParents());
        static::assertEquals($resource, $this->recorder->getResource($resource));
        static::assertCount(1, $resource->parents());
        static::assertInstanceOf(ResourceInterface::class, $this->recorder->getResource('parent'));

        $resource->removeParent($this->recorder->getResource('parent'));
        static::assertFalse($resource->hasParents());
    }

    /**
     * @test
     */
    public function removeChild(): void
    {
        $resource = new Resource('test');
        $this->recorder->addResource($resource, ['parent']);

        static::assertCount(1, $this->recorder->getResource('parent')->children());
        $this->recorder->getResource('parent')->removeChildren($resource);
        static::assertCount(0, $this->recorder->getResource('parent')->children());
    }

    /**
     * @test
     */
    public function addResourceThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource [test] already exists');
        $resource = new Resource('test');
        $this->recorder->addResource($resource);
        $this->recorder->addResource($resource);
    }

    /**
     * @test
     */
    public function removeResourceThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource [test] not found, can not remove');
        $this->recorder->removeResource('test');
    }

    /**
     * @test
     */
    public function getResourceThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource [test] not found');
        $this->recorder->getResource('test');
    }
}
