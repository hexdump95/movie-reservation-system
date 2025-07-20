<?php

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class JWTService
{
    private JWTTokenManagerInterface $tokenManager;
    private JWSProviderInterface $jwsProvider;
    private int $jwtExpiration;

    public function __construct(JWTTokenManagerInterface $tokenManager, JWSProviderInterface $jwsProvider, ContainerBagInterface $containerBag)
    {
        $this->tokenManager = $tokenManager;
        $this->jwsProvider = $jwsProvider;
        $this->jwtExpiration = $containerBag->get('jwt_expiration');
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

    public function getJwtExpiration(): int
    {
        return $this->jwtExpiration;
    }

}