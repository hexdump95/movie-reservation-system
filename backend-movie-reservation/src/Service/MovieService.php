<?php

namespace App\Service;

use App\DTO\AvailableShowtimeResponse;
use App\DTO\MovieDetailResponse;
use App\Repository\MovieRepository;
use Psr\Log\LoggerInterface;

class MovieService
{
    private MovieRepository $movieRepository;
    private LoggerInterface $logger;

    public function __construct(MovieRepository $movieRepository, LoggerInterface $logger)
    {
        $this->movieRepository = $movieRepository;
        $this->logger = $logger;
    }

    public function getUpcomingMovies(int $page): array
    {
        return $this->movieRepository->findUpcomingMovies($page, 10);
    }

    public function getMovieDetail(int $id): MovieDetailResponse
    {
        $movie = $this->movieRepository->getMovieDetail($id);
        if (!$movie) {
            throw new \Exception("Movie not found"); // TODO: Throw another exception
        }
        $showtimesResponse = [];
        foreach ($movie->getShowtimes() as $showtime) {
            if ($showtime->getDateStart() < new \DateTime()) {
                continue;
            }
            $showtimeResponse = (new AvailableShowtimeResponse())
                ->setId($showtime->getId())
                ->setDate($showtime->getDateStart())
                ->setTheaterNumber($showtime->getTheater()->getNumber());
            $showtimesResponse[] = $showtimeResponse;
        }
        return (new MovieDetailResponse())
            ->setTitle($movie->getTitle())
            ->setDescription($movie->getDescription())
            ->setYear($movie->getYear())
            ->setPosterImage($movie->getPosterImage())
            ->setGenreName($movie->getGenre()->getName())
            ->setShowtimes($showtimesResponse);
    }

}
