<?php

namespace App\DTO;

class CreateTheaterRequest
{
    private int $number;
    private array $seatsGrid = [];

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;
        return $this;
    }

    public function getSeatsGrid(): array
    {
        return $this->seatsGrid;
    }

    public function setSeatsGrid(array $seatsGrid): static
    {
        $this->seatsGrid = $seatsGrid;
        return $this;
    }
}