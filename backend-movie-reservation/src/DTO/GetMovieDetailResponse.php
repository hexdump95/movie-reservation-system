<?php

namespace App\DTO;

class GetMovieDetailResponse
{
    private ?string $title;
    private ?string $description;
    private ?string $posterImage;
    private ?int $duration;
    private ?\DateTime $releaseDate;
    private ?int $year;
    private GetMovieDetailGenreResponse $genre;
    private array $showtimes = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPosterImage(): ?string
    {
        return $this->posterImage;
    }

    public function setPosterImage(string $posterImage): static
    {
        $this->posterImage = $posterImage;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;
        return $this;
    }

    public function getGenre(): GetMovieDetailGenreResponse
    {
        return $this->genre;
    }

    public function setGenre(GetMovieDetailGenreResponse $genre): static
    {
        $this->genre = $genre;
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
