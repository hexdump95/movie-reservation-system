<?php

namespace App\DTO;

class UpcomingMovieResponse
{
    private ?int $id;
    private ?string $title;
    private bool $hasShowtime;

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

    public function getHasShowtime(): bool
    {
        return $this->hasShowtime;
    }

    public function setHasShowtime(bool $hasShowtime): static
    {
        $this->hasShowtime = $hasShowtime;
        return $this;
    }

}