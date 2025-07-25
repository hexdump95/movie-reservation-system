<?php

namespace App\DTO;

class UserAndRolesResponse
{
    private array $users;
    private array $roles;

    public function getUsers(): array
    {
        return $this->users;
    }

    public function setUsers(array $users): static
    {
        $this->users = $users;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }
}