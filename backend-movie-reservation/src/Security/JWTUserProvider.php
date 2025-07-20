<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class JWTUserProvider implements UserProviderInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $payload = $request?->attributes->get('jwt_payload', []);

        return new CustomJWTUser(
            $identifier,
            $payload['roles'] ?? ['ROLE_USER'],
            $payload['permissions'] ?? []
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof CustomJWTUser) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return CustomJWTUser::class === $class;
    }
}
