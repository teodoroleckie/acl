<?php

namespace Tleckie\Acl\Tests\Acl;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tleckie\Acl\Acl\Register;
use Tleckie\Acl\Acl\RegisterInterface;
use Tleckie\Acl\Role;
use Tleckie\Acl\Role\RoleFactory;
use Tleckie\Acl\Role\RoleRecorder;
use Tleckie\Acl\Role\RoleRecorderInterface;

/**
 * Class RegisterTest
 *
 * @package Tleckie\Acl\Tests
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class RegisterTest extends TestCase
{
    /** @var RegisterInterface */
    private RegisterInterface $register;

    /** @var RoleRecorderInterface */
    private RoleRecorderInterface $recorder;

    public function setUp(): void
    {
        $this->recorder = new RoleRecorder(new RoleFactory());
        $this->register = new Register($this->recorder);
    }

    /**
     * @test
     */
    public function addRole(): void
    {
        $admin = new Role('admin');
        $edit = new Role('edit');
        $list = new Role('list');

        $this->recorder->addRole($admin);
        $this->recorder->addRole($edit);
        $this->recorder->addRole($list);

        $this->register->addRole($admin);
        $this->register->addRole($edit, [$admin]);
        $this->register->addRole($list, [$admin, $edit]);

        static::assertCount(3, $this->register->rules()['roles']);
        static::assertCount(1, $this->register->rules()['roles']['edit']['parents']);
        static::assertCount(2, $this->register->rules()['roles']['list']['parents']);
    }

    /**
     * @test
     */
    public function addParentRoleThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->register->addRole(new Role('admin'), [new Role('edit')]);
    }
}
