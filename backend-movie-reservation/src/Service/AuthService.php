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
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    private UserRepository $userRepository;
    private RoleRepository $roleRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $tokenManager;
    private JWSProviderInterface $jwsProvider;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $tokenManager, JWSProviderInterface $jwsProvider)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->passwordHasher = $passwordHasher;
        $this->tokenManager = $tokenManager;
        $this->jwsProvider = $jwsProvider;
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

    public function generateToken($user): string
    {
        $jwtUser = new JWTUser($user->getEmail(), $user->getRoles());

        return $this->tokenManager->createFromPayload($jwtUser, ['permissions' => $user->getPermissions()]);
    }

    public function isTokenValid(string $token): bool
    {
        $loadedJws = $this->jwsProvider->load($token);
        return (!$loadedJws->isExpired() && !$loadedJws->isInvalid() && $loadedJws->isVerified());
    }

}
