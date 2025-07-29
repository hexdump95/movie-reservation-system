<?php

namespace App\DTO;

class AddShowtimeResponse
{
    private int $id;
    private \DateTime $dateStart;
    private \DateTime $dateEnd;
    private int $theaterId;
    private int $theaterNumber;
    private bool $hasBooks;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getDateStart(): \DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTime $dateStart): static
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getDateEnd(): \DateTime
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTime $dateEnd): static
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getTheaterId(): int
    {
        return $this->theaterId;
    }

    public function setTheaterId(int $theaterId): static
    {
        $this->theaterId = $theaterId;
        return $this;
    }

    public function getTheaterNumber(): int
    {
        return $this->theaterNumber;
    }

    public function setTheaterNumber(int $theaterNumber): static
    {
        $this->theaterNumber = $theaterNumber;
        return $this;
    }

    public function getHasBooks(): bool
    {
        return $this->hasBooks;
    }

    public function setHasBooks(bool $hasBooks): static
    {
        $this->hasBooks = $hasBooks;
        return $this;
    }

}
