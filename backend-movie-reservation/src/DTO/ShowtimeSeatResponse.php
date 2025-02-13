<?php

namespace App\DTO;

class ShowtimeSeatResponse
{
    private ?int $id;
    private ?int $column;
    private ?int $row;
    private ?string $code;
    private ?bool $isOccupied;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getColumn(): ?int
    {
        return $this->column;
    }

    public function setColumn(?int $column): static
    {
        $this->column = $column;
        return $this;
    }

    public function getRow(): ?int
    {
        return $this->row;
    }

    public function setRow(?int $row): static
    {
        $this->row = $row;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getIsOccupied(): ?bool
    {
        return $this->isOccupied;
    }

    public function setIsOccupied(?bool $isOccupied): static
    {
        $this->isOccupied = $isOccupied;
        return $this;
    }

}