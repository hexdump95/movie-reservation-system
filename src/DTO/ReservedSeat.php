<?php

namespace App\DTO;

class ReservedSeat
{
    private int $id;
    private float $price;
    private string $seatCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getSeatCode(): ?string
    {
        return $this->seatCode;

    }

    public function setSeatCode(?string $seatCode): static
    {
        $this->seatCode = $seatCode;
        return $this;
    }

}