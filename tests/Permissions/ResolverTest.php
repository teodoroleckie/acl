<?php

namespace Tleckie\Acl\Tests\Permissions;

use PHPUnit\Framework\TestCase;
use Tleckie\Acl\Permissions\Resolver;

class ResolverTest extends TestCase
{
    /** @var Resolver */
    private Resolver $resolver;

    private array $rules = [
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

    public function setUp(): void
    {
        $this->resolver = new Resolver();
    }

    /**
     * @test
     */
    public function check(): void
    {
        $bool = $this->resolver->isAllowed($this->rules, 'edit', 'key-3', 'edit');
        static::assertTrue($bool);

        $bool = $this->resolver->isAllowed($this->rules, 'edit', 'key-4');
        static::assertTrue($bool);

        $bool = $this->resolver->isAllowed($this->rules, 'edit', 'key-4', 'edit');
        static::assertTrue($bool);

        $bool = $this->resolver->isAllowed($this->rules, 'edit', 'key-2', 'edit');
        static::assertFalse($bool);

        $bool = $this->resolver->isAllowed($this->rules, 'edit', 'key-5');
        static::assertFalse($bool);

        $bool = $this->resolver->isAllowed($this->rules, 'edit', 'key-5', 'edit');
        static::assertFalse($bool);
    }
}
