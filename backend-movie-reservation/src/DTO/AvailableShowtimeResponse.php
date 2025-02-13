<?php

namespace App\DTO;

class AvailableShowtimeResponse
{
    private ?int $id;
    private ?\DateTimeInterface $date;
    private ?int $theaterNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getTheaterNumber(): ?int
    {
        return $this->theaterNumber;
    }

    public function setTheaterNumber(?int $theaterNumber): static
    {
        $this->theaterNumber = $theaterNumber;
        return $this;
    }


}