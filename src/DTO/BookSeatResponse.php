<?php

namespace App\DTO;

class BookSeatResponse
{
    private int $id;
    private string $userEmail;
    private \DateTimeInterface $showtimeDate;
    private string $movieTitle;
    private int $movieDuration;
    private float $totalPrice;
    private array $seats = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): static
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    public function getShowtimeDate(): \DateTimeInterface
    {
        return $this->showtimeDate;
    }

    public function setShowtimeDate(\DateTimeInterface $showtimeDate): static
    {
        $this->showtimeDate = $showtimeDate;
        return $this;
    }

    public function getMovieTitle(): string
    {
        return $this->movieTitle;
    }

    public function setMovieTitle(string $movieTitle): static
    {
        $this->movieTitle = $movieTitle;
        return $this;
    }

    public function getMovieDuration(): int
    {
        return $this->movieDuration;
    }

    public function setMovieDuration(int $movieDuration): static
    {
        $this->movieDuration = $movieDuration;
        return $this;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;
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
