<?php

namespace Tleckie\Acl\Tests\Role;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tleckie\Acl\Role;
use Tleckie\Acl\Role\RoleFactory;
use Tleckie\Acl\Role\RoleRecorder;

/**
 * Class RoleRecorderTest
 *
 * @package Tleckie\Acl\Tests\Roles
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class RoleRecorderTest extends TestCase
{
    /** @var RoleRecorder */
    private RoleRecorder $recorder;

    public function setUp(): void
    {
        $this->recorder = new RoleRecorder(new RoleFactory());
    }

    /**
     * @test
     */
    public function addRole(): void
    {
        $role = new Role('test');
        $this->recorder->addRole($role, ['parent']);
        static::assertEquals($role, $this->recorder->getRole($role));
        static::assertTrue($role->hasParents());
        static::assertCount(1, $role->parents());
        static::assertCount(1, $this->recorder->getRole('parent')->children());

        $role->removeParent($this->recorder->getRole('parent'));
        static::assertFalse($role->hasParents());
    }

    /**
     * @test
     */
    public function removeChild(): void
    {
        $resource = new Role('test');
        $this->recorder->addRole($resource, ['parent']);

        static::assertCount(1, $this->recorder->getRole('parent')->children());
        $this->recorder->getRole('parent')->removeChildren($resource);
        static::assertCount(0, $this->recorder->getRole('parent')->children());
    }

    /**
     * @test
     */
    public function addRoleThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Role [test] already exists');
        $role = new Role('test');
        $this->recorder->addRole($role);
        $this->recorder->addRole($role);
    }

    /**
     * @test
     */
    public function removeRoleThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Role [test] not found, can not remove');
        $this->recorder->removeRole('test');
    }

    /**
     * @test
     */
    public function getRoleThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Role [test] not found');
        $this->recorder->getRole('test');
    }
}
