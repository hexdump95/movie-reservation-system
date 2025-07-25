<?php

namespace App\DTO;

class UpdateRolesRequest
{
    private array $roles = [];

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