<?php

namespace App\DTO;

class GetTheaterDetailResponse
{
    private int $number;
    private array $seats = [];

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;
        return $this;
    }

    public function getSeats(): array
    {
        return $this->seats;
    }

    public function setSeats(array $seats): static
    {
        $this->seats = $seats;
        return $this;
    }
}
