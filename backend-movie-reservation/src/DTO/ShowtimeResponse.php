<?php

namespace App\DTO;

class ShowtimeResponse
{
    private ?string $movieTitle;
    private ?string $theaterNumber;
    private ?string $dateStart;
    private array $seats = [];

    public function getMovieTitle(): ?string
    {
        return $this->movieTitle;
    }

    public function setMovieTitle(?string $movieTitle): static
    {
        $this->movieTitle = $movieTitle;
        return $this;
    }

    public function getTheaterNumber(): ?string
    {
        return $this->theaterNumber;
    }

    public function setTheaterNumber(?string $theaterNumber): static
    {
        $this->theaterNumber = $theaterNumber;
        return $this;
    }

    public function getDateStart(): ?string
    {
        return $this->dateStart;
    }

    public function setDateStart(?string $dateStart): static
    {
        $this->dateStart = $dateStart;
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