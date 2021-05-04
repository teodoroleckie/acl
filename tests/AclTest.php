<?php

namespace Tleckie\Acl\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tleckie\Acl\Acl;
use Tleckie\Acl\Acl\OperationEnum;
use Tleckie\Acl\Acl\PermissionTypeEnum;
use Tleckie\Acl\Resource;
use Tleckie\Acl\Resource\ResourceInterface;
use Tleckie\Acl\Role;

/**
 * Class AclTest
 *
 * @package Tleckie\Acl\Tests
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class AclTest extends TestCase
{
    /** @var Acl */
    private Acl $acl;

    public function setUp(): void
    {
        $this->acl = new Acl();
    }

    /**
     * @test
     */
    public function generic(): void
    {
        $role = new Role('user');
        $objectId = spl_object_id($role);

        $this->acl->addRole($role);
        $this->acl->addResource('RESOURCE-0');

        static::assertTrue($this->acl->getResource('RESOURCE-0') instanceof ResourceInterface);

        static::assertTrue($this->acl->hasResource('RESOURCE-0'));

        $this->acl->allow(['user'], ['RESOURCE-0']);

        static::assertFalse($this->acl->hasRole('NONE'));

        static::assertEquals($objectId, spl_object_id($this->acl->getRole('user')));

        static::assertTrue($this->acl->hasRole('user'));

        static::assertTrue($this->acl->isAllowed('user', 'RESOURCE-0'));

        $this->acl->removeRole('user');

        static::assertFalse($this->acl->isAllowed('user', 'RESOURCE-0'));

        static::assertFalse($this->acl->hasRole('user'));
    }

    /**
     * @test
     * @dataProvider removeDataProvider
     * @param $objectId
     */
    public function removeRole($objectId): void
    {
        $this->acl->addRole('user-' . $objectId);
        $this->acl->addResource('resource-1');
        $this->acl->allow(['user-' . $objectId], ['resource-1']);
        static::assertTrue($this->acl->isAllowed('user-' . $objectId, 'resource-1'));
        static::assertTrue(isset($this->acl->rules()['roles']['user-'.$objectId]));

        $this->acl->removeRole('user-' . $objectId);
        static::assertFalse(isset($this->acl->rules()['roles']['user-'.$objectId]));
        static::assertFalse($this->acl->hasRole('user-'.$objectId));
        static::assertFalse($this->acl->isAllowed('user-' . $objectId, 'resource-1'));
    }

    /**
     * @test
     */
    public function removeParentRole(): void
    {
        $role = new Role('admin');

        $this->acl->addRole($role);
        $this->acl->addRole('user', ['admin']);

        static::assertTrue(isset($this->acl->rules()['roles']['admin']));
        static::assertTrue(isset($this->acl->rules()['roles']['user']['parents']['admin']));

        static::assertCount(2, $this->acl->roles());

        $this->acl->removeRole($role);
        static::assertCount(1, $this->acl->roles());

        static::assertFalse(isset($this->acl->rules()['roles']['admin']));
        static::assertFalse(isset($this->acl->rules()['roles']['user']['parents']['admin']));
    }

    /**
     * @test
     */
    public function removeParentResource(): void
    {
        $this->acl->addRole('admin');
        $this->acl->addRole('user', ['admin']);

        $resource = new Resource('r-1');
        $this->acl->addResource($resource);
        $this->acl->addResource('r-2', ['r-1']);

        $this->acl->allow(['admin'], ['r-1']);


        static::assertTrue(isset($this->acl->rules()['roles']['admin']['ALLOW']['r-2']));
        static::assertTrue(isset($this->acl->rules()['roles']['user']['parents']['admin']));



        static::assertCount(2, $this->acl->resources());
        $this->acl->removeResource($resource);
        static::assertCount(1, $this->acl->resources());

        static::assertFalse(isset($this->acl->rules()['roles']['admin']['ALLOW']['r-1']));
    }
    /**
     * @test
     */
    public function removeDenyParentResource(): void
    {
        $this->acl->addRole('admin');
        $this->acl->addRole('user', ['admin']);

        $resource = new Resource('r-1');
        $this->acl->addResource($resource);
        $this->acl->addResource('r-2', ['r-1']);

        $this->acl->deny(['admin'], ['r-1']);
        static::assertTrue(isset($this->acl->rules()['roles']['admin']['DENY']['r-2']));
        static::assertTrue(isset($this->acl->rules()['roles']['user']['parents']['admin']));
        $this->acl->removeResource($resource);
        static::assertFalse(isset($this->acl->rules()['roles']['admin']['DENY']['r-1']));
    }

    /**
     * @test
     * @dataProvider removeDataProvider
     * @param $objectId
     */
    public function removeResource($objectId): void
    {
        $this->acl->addRole('user-1');
        $this->acl->addResource('resource-' . $objectId);

        $this->acl->allow(['user-1'], ['resource-' . $objectId]);

        static::assertTrue(isset($this->acl->rules()['roles']['user-1']["ALLOW"]["resource-".$objectId]));
        static::assertTrue($this->acl->isAllowed('user-1', 'resource-' . $objectId));

        $this->acl->removeResource('resource-' . $objectId);
        static::assertFalse(isset($this->acl->rules()['roles']['user-1']["ALLOW"]["resource-".$objectId]));
        static::assertFalse($this->acl->hasResource('resource-' . $objectId));
        static::assertFalse($this->acl->isAllowed('user-' . $objectId, 'resource-' . $objectId));
    }

    public function removeDataProvider(): array
    {
        return [
            [4], [2], [3], [5]
        ];
    }

    /**
     * @test
     */
    public function removeAllRole(): void
    {
        foreach ([1, 2, 3, 4] as $id) {
            $this->acl->addRole('user-' . $id);
            $this->acl->addResource('resource-' . $id);
            $this->acl->allow(['user-' . $id], ['resource-' . $id]);
            static::assertTrue($this->acl->isAllowed('user-' . $id, 'resource-' . $id));
        }

        $this->acl->removeAllRole();

        foreach ([1, 2, 3, 4] as $id) {
            static::assertFalse($this->acl->hasRole('user-' . $id));
            static::assertFalse($this->acl->isAllowed('user-' . $id, 'resource-' . $id));
        }
    }

    /**
     * @test
     */
    public function removeAllResource(): void
    {
        $this->acl->addRole('user');

        $resource = new Resource('resource-1');

        $this->acl->addResource($resource);
        $this->acl->addResource('resource-2', ['resource-1']);

        $this->acl->deny(['user'], ['resource-1']);

        static::assertTrue(isset($this->acl->rules()['roles']['user']["DENY"]["resource-1"]));
        static::assertTrue(isset($this->acl->rules()['roles']['user']["DENY"]["resource-2"]));

        $this->acl->removeAllResource();

        static::assertFalse(isset($this->acl->rules()['roles']['user']["DENY"]["resource-1"]));
        static::assertFalse(isset($this->acl->rules()['roles']['user']["DENY"]["resource-2"]));
    }

    /**
     * @test
     */
    public function removeAllResourceThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->acl->addRole('user');

        $resource = new Resource('resource-1');

        $this->acl->addResource($resource);
        $this->acl->addResource('resource-2', ['resource-1']);

        $this->acl->removeAllResource();
        $this->acl->getResource($resource);
    }

    /**
     * @test
     */
    public function removeDeny(): void
    {
        $this->acl->addRole('user');
        $this->acl->addResource('resource-1');
        $this->acl->addResource('resource-2', ['resource-1']);

        $this->acl->deny(['user'], ['resource-1'], ['view']);

        static::assertTrue(isset($this->acl->rules()['roles']['user']["DENY"]["resource-1"]));
        static::assertTrue(isset($this->acl->rules()['roles']['user']["DENY"]["resource-2"]));

        $this->acl->removeDeny(['user'], ['resource-1'], ['view']);

        static::assertFalse(isset($this->acl->rules()['roles']['user']["DENY"]["resource-1"]));
        static::assertTrue(isset($this->acl->rules()['roles']['user']["DENY"]["resource-2"]));
    }

    /**
     * @test
     */
    public function removeAllow(): void
    {
        $this->acl->addRole('user');
        $this->acl->addResource('resource-1');
        $this->acl->addResource('resource-2', ['resource-1']);

        $this->acl->allow(['user'], ['resource-1'], ['view']);

        static::assertTrue(isset($this->acl->rules()['roles']['user']["ALLOW"]["resource-1"]));
        static::assertTrue(isset($this->acl->rules()['roles']['user']["ALLOW"]["resource-2"]));

        $this->acl->removeAllow(['user'], ['resource-1'], ['view']);

        static::assertFalse(isset($this->acl->rules()['roles']['user']["ALLOW"]["resource-1"]));
        static::assertTrue(isset($this->acl->rules()['roles']['user']["ALLOW"]["resource-2"]));
    }

    /**
     * @test
     */
    public function setRule(): void
    {
        $admin = new Role('admin');
        $user = new Role('user');
        $r1 = new Resource('r-1');
        $r2 = new Resource('r-2');

        $this->acl->addRole($admin);
        $this->acl->addRole($user);
        $this->acl->addResource($r2);
        $this->acl->addResource($r1, [$r2]);

        $roles = [new Role('user'), $admin];
        $resources = [$r1, $r2];

        $this->acl->setRule(
            OperationEnum::ADD(),
            PermissionTypeEnum::ALLOW(),
            $roles,
            $resources,
            ['view']
        );

        static::assertTrue(isset($this->acl->rules()['roles']['admin']['parents']));
        static::assertTrue(isset($this->acl->rules()['roles']['admin']['DENY']));
        static::assertTrue(isset($this->acl->rules()['roles']['admin']['ALLOW']['r-1']['view']));

        static::assertTrue(isset($this->acl->rules()['roles']['admin']['parents']));
        static::assertTrue(isset($this->acl->rules()['roles']['admin']['DENY']));
        static::assertTrue(isset($this->acl->rules()['roles']['admin']['ALLOW']['r-1']['view']));
    }

    /**
     * @test
     * @dataProvider allowDataProvider
     * @param mixed       $role
     * @param mixed       $resource
     * @param string|null $privilege
     * @param bool        $isAllowed
     */
    public function allow(
        mixed $role,
        mixed $resource,
        string $privilege = null,
        bool $isAllowed = true,
    ): void {
        $this->configureAccess();
        $this->acl->allow(['USER-0'], ['RESOURCE-0']);
        $this->acl->allow(['USER-3'], ['RESOURCE-3'], ['view', 'edit', 'list']);

        static::assertEquals($isAllowed, $this->acl->isAllowed($role, $resource, $privilege));
    }

    private function configureAccess(): void
    {
        $this->acl->addRole('USER-0');
        $this->acl->addRole('USER-1', ['USER-0']);
        $this->acl->addRole(new Role('USER-2'), ['USER-1']);
        $this->acl->addRole(new Role('USER-3'));

        $this->acl->addResource('RESOURCE-0');
        $this->acl->addResource(new Resource('RESOURCE-1'), ['RESOURCE-0']);
        $this->acl->addResource('RESOURCE-2', ['RESOURCE-1']);
        $this->acl->addResource('RESOURCE-3', ['RESOURCE-2']);
        $this->acl->addResource('RESOURCE-4', ['RESOURCE-3']);
    }

    /**
     * @test
     * @dataProvider denyDataProvider
     * @param mixed       $role
     * @param mixed       $resource
     * @param string|null $privilege
     * @param bool        $isAllowed
     */
    public function deny(
        mixed $role,
        mixed $resource,
        string $privilege = null,
        bool $isAllowed = true,
    ): void {
        $this->configureAccess();

        $this->acl->allow(['USER-0'], ['RESOURCE-1']);
        $this->acl->allow(['USER-1'], ['RESOURCE-3'], ['view', 'edit', 'list']);
        $this->acl->deny(['USER-1'], ['RESOURCE-3'], ['delete']);
        $this->acl->deny(['USER-2'], ['RESOURCE-3']);

        static::assertEquals($isAllowed, $this->acl->isAllowed($role, $resource, $privilege));
    }

    /**
     * @return array
     */
    public function denyDataProvider(): array
    {
        return [
            [new Role('USER-0'), 'RESOURCE-0', null, false],
            [new Role('USER-1'), 'RESOURCE-3', null, true],
            ['USER-1', 'RESOURCE-3', null, true],
            [new Role('USER-1'), 'RESOURCE-3', 'delete', false],
            [new Role('USER-1'), 'RESOURCE-3', 'view', true],
            [new Role('USER-2'), 'RESOURCE-3', 'view', false],
            ['USER-2', 'RESOURCE-3', null, false],
            ['USER-3', 'RESOURCE-3', null, false],
            ['USER-3', 'RESOURCE-4', null, false],
            ['USER-3', 'RESOURCE-4', 'view', false],
        ];
    }

    /**
     * @return array
     */
    public function allowDataProvider(): array
    {
        return [
            [new Role('USER-0'), 'NONE', null, false],
            ['USER-0', 'NONE', 'NONE', false],
            [new Role('USER-0'), new Resource('RESOURCE-0'), null, true],
            ['USER-0', 'RESOURCE-1', null, true],
            ['USER-0', 'RESOURCE-2', null, true],
            ['USER-0', 'RESOURCE-3', null, true],
            ['USER-0', 'RESOURCE-4', null, true],
            ['USER-0', 'RESOURCE-0', 'view', true],
            ['USER-0', 'RESOURCE-1', 'view', true],
            ['USER-0', 'RESOURCE-2', 'view', true],
            ['USER-0', 'RESOURCE-3', 'view', true],
            ['USER-0', 'RESOURCE-4', 'view', true],

            ['USER-1', 'NONE', null, false],
            ['USER-1', 'NONE', 'NONE', false],
            ['USER-1', 'RESOURCE-0', null, true],
            ['USER-1', 'RESOURCE-1', null, true],
            ['USER-1', 'RESOURCE-2', null, true],
            ['USER-1', new Resource('RESOURCE-3'), null, true],
            ['USER-1', 'RESOURCE-4', null, true],
            ['USER-1', 'RESOURCE-0', 'view', true],
            ['USER-1', 'RESOURCE-1', 'view', true],
            ['USER-1', 'RESOURCE-2', 'view', true],
            ['USER-1', 'RESOURCE-3', 'view', true],
            ['USER-1', 'RESOURCE-4', 'view', true],

            ['USER-2', 'NONE', null, false],
            ['USER-2', 'NONE', 'NONE', false],
            ['USER-2', 'RESOURCE-0', null, true],
            ['USER-2', 'RESOURCE-1', null, true],
            ['USER-2', 'RESOURCE-2', null, true],
            ['USER-2', 'RESOURCE-3', null, true],
            ['USER-2', 'RESOURCE-4', null, true],
            ['USER-2', 'RESOURCE-0', 'view', true],
            ['USER-2', 'RESOURCE-1', 'view', true],
            ['USER-2', 'RESOURCE-2', 'view', true],
            ['USER-2', 'RESOURCE-3', 'view', true],
            ['USER-2', 'RESOURCE-4', 'view', true],

            ['USER-3', 'NONE', null, false],
            ['USER-3', 'NONE', 'NONE', false],
            ['USER-3', 'RESOURCE-3', 'view', true],
            ['USER-3', 'RESOURCE-3', 'edit', true],
            ['USER-3', 'RESOURCE-3', 'list', true],
            ['USER-3', 'RESOURCE-3', 'delete', false],

        ];
    }
}
