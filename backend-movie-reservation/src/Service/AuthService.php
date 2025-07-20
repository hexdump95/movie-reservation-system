<?php

namespace App\Service;

use App\DTO\RegisterRequest;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserRole;
use App\Enum\RoleEnum;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordHasher = $passwordHasher;
    }
    public function register(RegisterRequest $request): ?User
    {
        if ($this->userRepository->existsByEmail($request->getUsername())) {
            return null; // user exists, throw an exception
        }

        $role = $this->roleRepository->findByName(RoleEnum::USER->name);
        if ($role === null) {
            $role = new Role();
            $role->setName(RoleEnum::USER->name);
            $this->roleRepository->save($role);
        }

        $userRole = new UserRole();
        $userRole->setRole($role);

        $user = new User();
        $user->addUserRole($userRole);
        $user->setEmail($request->getUsername());
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $request->getPassword()
        );
        $user->setPasswordHash($hashedPassword);
        return $this->userRepository->save($user);
    }

}
