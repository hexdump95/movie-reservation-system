<?php

namespace App\DTO;

class ReservationDetailResponse
{
    private float $bookTotalPrice;
    private string $bookStatus;
    private \DateTimeInterface $bookCreatedAt;
    private \DateTimeInterface $showtimeDateStart;
    private string $movieTitle;
    private int $theaterNumber;
    private array $seats = [];

    public function getBookTotalPrice(): float
    {
        return $this->bookTotalPrice;
    }

    public function setBookTotalPrice(float $bookTotalPrice): self
    {
        $this->bookTotalPrice = $bookTotalPrice;
        return $this;
    }

    public function getBookStatus(): string
    {
        return $this->bookStatus;
    }

    public function setBookStatus(string $bookStatus): self
    {
        $this->bookStatus = $bookStatus;
        return $this;
    }

    public function getBookCreatedAt(): \DateTimeInterface
    {
        return $this->bookCreatedAt;
    }

    public function setBookCreatedAt(\DateTimeInterface $bookCreatedAt): self
    {
        $this->bookCreatedAt = $bookCreatedAt;
        return $this;
    }

    public function getShowtimeDateStart(): \DateTimeInterface
    {
        return $this->showtimeDateStart;
    }

    public function setShowtimeDateStart(\DateTimeInterface $showtimeDateStart): self
    {
        $this->showtimeDateStart = $showtimeDateStart;
        return $this;
    }

    public function getMovieTitle(): string
    {
        return $this->movieTitle;
    }

    public function setMovieTitle(string $movieTitle): self
    {
        $this->movieTitle = $movieTitle;
        return $this;
    }

    public function getTheaterNumber(): int
    {
        return $this->theaterNumber;
    }

    public function setTheaterNumber(int $theaterNumber): self
    {
        $this->theaterNumber = $theaterNumber;
        return $this;
    }

    public function getSeats(): array
    {
        return $this->seats;
    }

    public function setSeats(array $seats): self
    {
        $this->seats = $seats;
        return $this;
    }

}
