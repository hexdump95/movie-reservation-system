<?php

namespace App\DTO;

class GetMovieDetailShowtimeResponse
{
    private int $id;
    private \DateTimeInterface $dateStart;
    private \DateTimeInterface $dateEnd;
    private int $theaterId;
    private int $theaterNumber;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getDateStart(): \DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getDateEnd(): \DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;
        return $this;
    }

    public function getTheaterId(): int
    {
        return $this->theaterId;
    }

    public function setTheaterId($theaterId): static
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

}
