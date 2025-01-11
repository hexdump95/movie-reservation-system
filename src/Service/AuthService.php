<?php

namespace App\Service;

use App\DTO\RegisterRequest;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserRole;
use App\Enum\RoleEnum;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $tokenManager;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $tokenManager)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordHasher = $passwordHasher;
        $this->tokenManager = $tokenManager;
    }

    public function register(RegisterRequest $request): ?string
    {
        if ($this->userRepository->findByEmail($request->getUsername()) !== null) {
            return null; // user not found, manage an exception
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
        $this->userRepository->save($user);

        $jwtUser = new JWTUser($user->getEmail(), $user->getRoles());

        return $this->tokenManager->create($jwtUser);
    }
}
