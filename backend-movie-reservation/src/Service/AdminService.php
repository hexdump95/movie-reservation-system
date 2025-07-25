<?php

namespace App\Service;

use App\DTO\UpdateRoleRequest;
use App\DTO\UpdateRolesRequest;
use App\DTO\UserAndRolesResponse;
use App\DTO\UserResponse;
use App\Entity\UserRole;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;

class AdminService
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private const RolePrefix = 'ROLE_';

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function getUsersAndRoles(): UserAndRolesResponse
    {
        $users = $this->userRepository->findAll();
        $roles = $this->roleRepository->findAllOrderById();
        $roleNames = [];
        foreach ($roles as $role) {
            $roleNames[] = $role->getName();
        }
        $usersResponse = [];
        foreach ($users as $user) {
            $roles = array_map(function ($role) {
                return substr($role, strlen(self::RolePrefix));
            }, $user->getRoles());
            $userResponse = (new UserResponse())
                ->setId($user->getId())
                ->setEmail($user->getEmail())
                ->setRoles($roles);
            $usersResponse[] = $userResponse;
        }
        return (new UserAndRolesResponse())
            ->setRoles($roleNames)
            ->setUsers($usersResponse);
    }

    public function updateRole(int $id, UpdateRoleRequest $roleRequest): bool
    {
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            return false;
        }
        $roleDb = $this->roleRepository->findByName($roleRequest->getRole());
        if ($roleDb === null) {
            return false;
        }

        $userRoles = $user->getUserRoles()->filter(function (UserRole $userRole) use ($roleRequest) {
            return $userRole->getRole()->getName() === $roleRequest->getRole();
        });

        if (count($userRoles) > 0) {
            $user->removeUserRole($userRoles->first());
        } else {
            $userRole = (new UserRole())
                ->setRole($roleDb)
                ->setUser($user);
            $user->addUserRole($userRole);
        }
        $this->userRepository->save($user);
        return true;
    }

    public function updateRoles(int $id, UpdateRolesRequest $roles): bool
    {
        $roles = array_unique($roles->getRoles());
        if (count($roles) === 0) {
            return false;
        }
        $user = $this->userRepository->findById($id);
        if ($user === null) {
            return false;
        }

        sort($roles);
        $userRoles = $user->getRoles();
        sort($userRoles);
        if ($roles === $userRoles) {
            return false;
        }

        $userRoles = new ArrayCollection();
        foreach ($roles as $role) {
            $role = substr($role, strlen(self::RolePrefix));
            $roleDb = $this->roleRepository->findByName($role);
            $userRole = (new UserRole())
                ->setRole($roleDb)
                ->setUser($user);
            $userRoles->add($userRole);
        }
        $user->setUserRoles($userRoles);
        $this->userRepository->save($user);
        return true;
    }

}
