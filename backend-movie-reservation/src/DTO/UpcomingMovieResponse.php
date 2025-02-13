<?php

namespace App\DTO;

class UpcomingMovieResponse
{
    private ?int $id;
    private ?string $title;
    private ?string $posterImage;
    private bool $hasShowtime;
    private ?string $genreName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
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

    public function getHasShowtime(): bool
    {
        return $this->hasShowtime;
    }

    public function setHasShowtime(bool $hasShowtime): static
    {
        $this->hasShowtime = $hasShowtime;
        return $this;
    }

    public function getGenreName(): ?string
    {
        return $this->genreName;
    }

    public function setGenreName(string $genreName): static
    {
        $this->genreName = $genreName;
        return $this;
    }

}