<?php

namespace App\DTO;

class MovieDetailResponse
{
    private ?string $title;
    private ?string $year;
    private ?string $description;
    private ?string $posterImage;
    private ?string $genreName;
    private array $showtimes = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): static
    {
        $this->year = $year;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPosterImage(): ?string
    {
        return $this->posterImage;
    }

    public function setPosterImage(?string $posterImage): static
    {
        $this->posterImage = $posterImage;
        return $this;
    }

    public function getGenreName(): ?string
    {
        return $this->genreName;
    }

    public function setGenreName(?string $genreName): static
    {
        $this->genreName = $genreName;
        return $this;
    }

    public function getShowtimes(): array
    {
        return $this->showtimes;
    }

    public function setShowtimes(array $showtimes): static
    {
        $this->showtimes = $showtimes;
        return $this;
    }

}