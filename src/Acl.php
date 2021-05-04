<?php

namespace Tleckie\Acl;

use Tleckie\Acl\Acl\AclInterface;
use Tleckie\Acl\Acl\OperationEnum;
use Tleckie\Acl\Acl\PermissionTypeEnum;
use Tleckie\Acl\Acl\Register;
use Tleckie\Acl\Acl\RulesInterface;
use Tleckie\Acl\Permissions\Permissions;
use Tleckie\Acl\Resource\ResourceFactory;
use Tleckie\Acl\Resource\ResourceFactoryInterface;
use Tleckie\Acl\Resource\ResourceInterface;
use Tleckie\Acl\Resource\ResourceRecorder;
use Tleckie\Acl\Resource\ResourceRecorderInterface;
use Tleckie\Acl\Role\RoleFactory;
use Tleckie\Acl\Role\RoleFactoryInterface;
use Tleckie\Acl\Role\RoleInterface;
use Tleckie\Acl\Role\RoleRecorder;
use Tleckie\Acl\Role\RoleRecorderInterface;

/**
 * Class Acl
 *
 * @package Tleckie\Acl
 * @author  Teodoro Leckie Westberg <teodoroleckie@gmail.com>
 */
class Acl implements AclInterface
{
    /** @var Register */
    protected Register $register;

    /** @var ResourceFactoryInterface */
    private ResourceFactoryInterface $resourceFactory;

    /** @var RoleFactoryInterface */
    private RoleFactoryInterface $roleFactory;

    /** @var ResourceRecorderInterface */
    private ResourceRecorderInterface $resourceRecorder;

    /** @var RoleRecorderInterface */
    private RoleRecorderInterface $roleRecorder;

    /**
     * Acl constructor.
     *
     * @param ResourceFactoryInterface|null  $resourceFactory
     * @param RoleFactoryInterface|null      $roleFactory
     * @param ResourceRecorderInterface|null $resourceRecorder
     * @param RoleRecorderInterface|null     $roleRecorder
     */
    public function __construct(
        ?ResourceFactoryInterface $resourceFactory = null,
        ?RoleFactoryInterface $roleFactory = null,
        ?ResourceRecorderInterface $resourceRecorder = null,
        ?RoleRecorderInterface $roleRecorder = null,
    ) {
        $this->resourceFactory = $this->resolveResourceFactory($resourceFactory);

        $this->roleFactory = $this->resolveRoleFactory($roleFactory);

        $this->resourceRecorder = $this->resolveResourceRecorder($resourceRecorder);

        $this->roleRecorder = $this->resolveRoleRecorder($roleRecorder);

        $this->register = new Register($this->roleRecorder, );
    }

    /**
     * @param ResourceFactoryInterface|null $resourceFactory
     * @return ResourceFactoryInterface
     */
    private function resolveResourceFactory(
        ResourceFactoryInterface|null $resourceFactory
    ): ResourceFactoryInterface {
        return ($resourceFactory) ?: new ResourceFactory();
    }

    /**
     * @param RoleFactoryInterface|null $roleFactory
     * @return RoleFactoryInterface
     */
    private function resolveRoleFactory(
        RoleFactoryInterface|null $roleFactory
    ): RoleFactoryInterface {
        return ($roleFactory) ?: new RoleFactory();
    }

    /**
     * @param ResourceRecorderInterface|null $resourceRecorder
     * @return ResourceRecorderInterface
     */
    private function resolveResourceRecorder(
        ResourceRecorderInterface|null $resourceRecorder
    ): ResourceRecorderInterface {
        return ($resourceRecorder) ?: new ResourceRecorder($this->resourceFactory);
    }

    /**
     * @param RoleRecorderInterface|null $roleRecorder
     * @return RoleRecorderInterface
     */
    private function resolveRoleRecorder(
        RoleRecorderInterface|null $roleRecorder
    ): RoleRecorderInterface {
        return ($roleRecorder) ?: new RoleRecorder($this->roleFactory);
    }

    /**
     * @param RoleInterface|string $role
     * @param array                $parents
     * @return AclInterface
     */
    public function addRole(RoleInterface|string $role, array $parents = []): AclInterface
    {
        $this->roleRecorder->addRole($role, $parents);

        $this->register->addRole($role, $parents);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRole(RoleInterface|string $role): RoleInterface
    {
        return $this->roleRecorder->getRole($role);
    }

    /**
     * @inheritdoc
     */
    public function hasRole(RoleInterface|string $role): bool
    {
        return $this->roleRecorder->hasRole($role);
    }

    /**
     * @param RoleInterface|string $role
     * @return AclInterface
     */
    public function removeRole(RoleInterface|string $role): AclInterface
    {
        $this->roleRecorder->removeRole($role);

        $this->register->removeRole($role);

        return $this;
    }

    /**
     * @return AclInterface
     */
    public function removeAllRole(): AclInterface
    {
        $this->roleRecorder->removeAllRole();

        $this->register->removeAllRole();

        return $this;
    }

    /**
     * @param string|ResourceInterface $resource
     * @param ResourceInterface[]      $parents
     * @return AclInterface
     */
    public function addResource(
        string|ResourceInterface $resource,
        array $parents = []
    ): AclInterface {
        $this->resourceRecorder->addResource($resource, $parents);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getResource(
        ResourceInterface|string $resource
    ): ResourceInterface {
        return $this->resourceRecorder->getResource($resource);
    }

    /**
     * @inheritdoc
     */
    public function hasResource(ResourceInterface|string $resource): bool
    {
        return $this->resourceRecorder->hasResource($resource);
    }

    /**
     * @param ResourceInterface|string $resource
     * @return AclInterface
     */
    public function removeResource(
        ResourceInterface|string $resource
    ): AclInterface {
        $this->resourceRecorder->removeResource($resource);

        $this->register->removeResource($resource);

        return $this;
    }

    /**
     * @return AclInterface
     */
    public function removeAllResource(): AclInterface
    {
        $this->resourceRecorder->removeAllResource();

        $this->register->removeAllResource();

        return $this;
    }

    /**
     * @param array $roles
     * @param array $resources
     * @param array $privileges
     * @return AclInterface
     */
    public function removeDeny(
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): AclInterface {
        return $this->setRule(
            OperationEnum::REMOVE(),
            PermissionTypeEnum::DENY(),
            $roles,
            $resources,
            $privileges
        );
    }

    /**
     * @param OperationEnum      $operation
     * @param PermissionTypeEnum $type
     * @param array              $roles
     * @param array              $resources
     * @param array              $privileges
     * @return AclInterface
     */
    public function setRule(
        OperationEnum $operation,
        PermissionTypeEnum $type,
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): AclInterface {
        $this->register->setRule(
            $operation,
            $type,
            $this->normalizeRoleCollection($roles),
            $this->normalizeResourceCollection($resources),
            $privileges
        );

        return $this;
    }

    /**
     * @param RoleInterface[] $roles
     * @return array
     */
    private function normalizeRoleCollection(array $roles): array
    {
        $collection = [];
        foreach ($roles as $role) {
            $collection[] = $this->roleRecorder->getRole($role);
        }

        return $collection;
    }

    /**
     * @param array $resources
     * @return ResourceInterface[]
     */
    private function normalizeResourceCollection(array $resources): array
    {
        $collection = [];
        foreach ($resources as $resource) {
            $collection[] = $this->resourceRecorder->getResource($resource);
        }

        return $collection;
    }

    /**
     * @param array $roles
     * @param array $resources
     * @param array $privileges
     * @return AclInterface
     */
    public function removeAllow(
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): AclInterface {
        return $this->setRule(
            OperationEnum::REMOVE(),
            PermissionTypeEnum::ALLOW(),
            $roles,
            $resources,
            $privileges
        );
    }

    /**
     * @param array $roles
     * @param array $resources
     * @param array $privileges
     * @return AclInterface
     */
    public function allow(
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): AclInterface {
        return $this->setRule(
            OperationEnum::ADD(),
            PermissionTypeEnum::ALLOW(),
            $roles,
            $resources,
            $privileges
        );
    }

    /**
     * @param array $roles
     * @param array $resources
     * @param array $privileges
     * @return AclInterface
     */
    public function deny(
        array $roles = [],
        array $resources = [],
        array $privileges = []
    ): AclInterface {
        return $this->setRule(
            OperationEnum::ADD(),
            PermissionTypeEnum::DENY(),
            $roles,
            $resources,
            $privileges
        );
    }

    /**
     * @inheritdoc
     */
    public function isAllowed(
        RoleInterface|string $role = null,
        ResourceInterface|string $resource = null,
        string $privilege = null
    ): bool {
        return $this->register->isAllowed($role, $resource, $privilege);
    }

    /**
     * @inheritdoc
     */
    public function roles(): array
    {
        return $this->roleRecorder->roles();
    }

    /**
     * @inheritdoc
     */
    public function resources(): array
    {
        return $this->resourceRecorder->resources();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return $this->register->rules();
    }
}
