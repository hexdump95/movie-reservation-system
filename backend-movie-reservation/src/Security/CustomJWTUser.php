<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class CustomJWTUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    private string $userIdentifier;
    private array $roles;
    private array $permissions;

    public function __construct(string $userIdentifier, array $roles = ['ROLE_USER'], array $permissions = [])
    {
        $this->userIdentifier = $userIdentifier;
        $this->roles = $roles;
        $this->permissions = $permissions;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

}
