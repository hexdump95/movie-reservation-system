<?php

namespace App\Service;

use App\DTO\GetGenreResponse;
use App\Repository\GenreRepository;

class GenreService
{
    private GenreRepository $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    public function getGenres(): array
    {
        $genres = $this->genreRepository->findAllWhereDeleteAtIsNull();
        $genresResponse = [];
        foreach ($genres as $genre) {
            $genreResponse = (new GetGenreResponse())
                ->setId($genre->getId())
                ->setName($genre->getName());
            $genresResponse[] = $genreResponse;
        }
        return $genresResponse;
    }
}
