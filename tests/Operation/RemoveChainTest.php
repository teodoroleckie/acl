<?php

namespace Tleckie\Acl\Tests\Operation;

use PHPUnit\Framework\TestCase;
use Tleckie\Acl\Acl\OperationEnum;
use Tleckie\Acl\Acl\PermissionTypeEnum;
use Tleckie\Acl\Operation\AddChain;
use Tleckie\Acl\Operation\OperationChainInterface;
use Tleckie\Acl\Operation\RemoveChain;
use Tleckie\Acl\Resource;
use Tleckie\Acl\Role;

class RemoveChainTest extends TestCase
{
    /** @var OperationChainInterface */
    private OperationChainInterface $chain;

    private array $rules;

    public function setUp(): void
    {
        $this->chain = new AddChain();
        $this->chain->next(new RemoveChain());

        $this->rules = [
            'roles' => [
                'admin' => [
                    'parents' => [],
                    'DENY' => [
                        'key-2' => ['view' => 'ALLOW', 'edit' => 'ALLOW', 'list' => 'ALLOW'],
                        'key-5' => []
                    ],
                    'ALLOW' => [
                        'key-1' => ['view' => 'ALLOW', 'edit' => 'ALLOW', 'list' => 'ALLOW'],
                        'key-2' => ['view' => 'ALLOW', 'edit' => 'ALLOW', 'list' => 'ALLOW'],
                        'key-3' => ['view' => 'ALLOW', 'edit' => 'ALLOW', 'list' => 'ALLOW'],
                        'key-4' => []
                    ]
                ],
                'edit' => [
                    'parents' => [
                        'admin' => []
                    ],
                    'DENY' => [

                    ],
                    'ALLOW' => [
                    ]
                ],
            ]
        ];
    }

    /**
     *
     */
    public function addAllowPrivileges(): void
    {
        $resource = new Resource('uri');
        $resource->addChildren(new Resource('child-uri'));

        $this->chain->handle(
            OperationEnum::ADD(),
            PermissionTypeEnum::ALLOW(),
            [new Role('edit')],
            [$resource],
            ['view', 'list', 'edit'],
            $this->rules
        );

        static::assertEquals('ALLOW', $this->rules['roles']['edit']['ALLOW']['uri']['view']);
        static::assertEquals('ALLOW', $this->rules['roles']['edit']['ALLOW']['uri']['list']);
        static::assertEquals('ALLOW', $this->rules['roles']['edit']['ALLOW']['uri']['edit']);

        static::assertEquals('ALLOW', $this->rules['roles']['edit']['ALLOW']['child-uri']['view']);
        static::assertEquals('ALLOW', $this->rules['roles']['edit']['ALLOW']['child-uri']['list']);
        static::assertEquals('ALLOW', $this->rules['roles']['edit']['ALLOW']['child-uri']['edit']);
    }

    /**
     *
     */
    public function addDenyPrivileges(): void
    {
        $this->chain->handle(
            OperationEnum::ADD(),
            PermissionTypeEnum::DENY(),
            [new Role('edit')],
            [new Resource('uri')],
            ['view', 'list', 'edit'],
            $this->rules
        );

        static::assertEquals('DENY', $this->rules['roles']['edit']['DENY']['uri']['view']);
        static::assertEquals('DENY', $this->rules['roles']['edit']['DENY']['uri']['list']);
        static::assertEquals('DENY', $this->rules['roles']['edit']['DENY']['uri']['edit']);
    }

    /**
     * @test
     */
    public function removeAllowPrivileges(): void
    {
        $resource = new Resource('uri');
        $resource->addChildren(new Resource('child-uri'));

        $this->chain->handle(
            OperationEnum::ADD(),
            PermissionTypeEnum::ALLOW(),
            [new Role('edit')],
            [$resource],
            [],
            $this->rules
        );

        static::assertEquals([], $this->rules['roles']['edit']['ALLOW']['uri']);
        static::assertEquals([], $this->rules['roles']['edit']['ALLOW']['child-uri']);

        $this->chain->handle(
            OperationEnum::REMOVE(),
            PermissionTypeEnum::ALLOW(),
            [new Role('edit')],
            [$resource],
            [],
            $this->rules
        );

        static::assertFalse(isset($this->rules['roles']['edit']['ALLOW']['uri']));
        static::assertTrue(isset($this->rules['roles']['edit']['ALLOW']['child-uri']));

        $resource = new Resource('uri');
        $resource->addChildren(new Resource('child-uri'));

        $this->chain->handle(
            OperationEnum::ADD(),
            PermissionTypeEnum::ALLOW(),
            [new Role('edit')],
            [$resource],
            ['view', 'edit'],
            $this->rules
        );

        static::assertTrue(isset($this->rules['roles']['edit']['ALLOW']['child-uri']['view']));
        static::assertTrue(isset($this->rules['roles']['edit']['ALLOW']['child-uri']['edit']));

        $this->chain->handle(
            OperationEnum::REMOVE(),
            PermissionTypeEnum::ALLOW(),
            [new Role('edit')],
            [$resource],
            ['view'],
            $this->rules
        );

        static::assertTrue(isset($this->rules['roles']['edit']['ALLOW']['uri']['edit']));
        static::assertFalse(isset($this->rules['roles']['edit']['ALLOW']['uri']['view']));

        static::assertTrue(isset($this->rules['roles']['edit']['ALLOW']['child-uri']['view']));
        static::assertTrue(isset($this->rules['roles']['edit']['ALLOW']['child-uri']['edit']));
    }
}
