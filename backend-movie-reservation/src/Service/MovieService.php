<?php

namespace App\Service;

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

}
