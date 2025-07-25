<?php

namespace App\DTO;

class UpdateRoleRequest
{
    private string $role;

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

}