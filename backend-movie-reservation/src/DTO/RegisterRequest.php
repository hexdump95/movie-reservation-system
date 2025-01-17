<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest
{
    #[Assert\NotBlank]
    private string $username;
    #[Assert\NotBlank]
    private string $password;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): RegisterRequest
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): RegisterRequest
    {
        $this->password = $password;
        return $this;
    }

}
