<?php

namespace App\Service;

use App\DTO\UpcomingMovieResponse;
use App\Repository\MovieRepository;

class MovieService
{
    private MovieRepository $movieRepository;

    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function getUpcomingMovies(): array
    {

        $movies = $this->movieRepository->findUpcomingMovies();

        $moviesDto = [];
        foreach ($movies as $movie) {
            $movieDto = (new UpcomingMovieResponse())
                ->setId($movie->getId())
                ->setTitle($movie->getTitle())
                ->setHasShowtime($movie->getShowtimes()->first()->getDateStart() > new \DateTime());
            $moviesDto[] = $movieDto;
        }
        return $moviesDto;
    }

}
