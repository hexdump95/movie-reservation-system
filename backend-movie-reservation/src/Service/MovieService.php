<?php

namespace App\Service;

use App\DTO\AvailableShowtimeResponse;
use App\DTO\CreateMovieRequest;
use App\DTO\GetMovieDetailGenreResponse;
use App\DTO\GetMovieDetailResponse;
use App\DTO\GetMovieDetailShowtimeResponse;
use App\DTO\GetMovieResponse;
use App\DTO\MovieDetailResponse;
use App\DTO\UpdateMovieRequest;
use App\Entity\Movie;
use App\Exception\ServiceException;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTimeImmutable;

class MovieService
{
    private MovieRepository $movieRepository;
    private GenreRepository $genreRepository;

    public function __construct(MovieRepository $movieRepository, GenreRepository $genreRepository)
    {
        $this->movieRepository = $movieRepository;
        $this->genreRepository = $genreRepository;
    }

    public function getUpcomingMovies(int $page): array
    {
        return $this->movieRepository->findUpcomingMovies($page, 10);
    }

    /**
     * @throws ServiceException
     */
    public function getUpcomingMovieDetail(int $id): MovieDetailResponse
    {
        $movie = $this->movieRepository->getMovieDetail($id);
        if (!$movie) {
            throw new ServiceException(['Movie with id ' . $id . ' not found']);
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

    public function getMovies(): array
    {
        $movies = $this->movieRepository->findAllWhereDeletedAtIsNull();
        $moviesResponse = [];
        foreach ($movies as $movie) {
            $movieResponse = (new GetMovieResponse())
                ->setId($movie->getId())
                ->setTitle($movie->getTitle())
                ->setYear($movie->getYear())
                ->setGenreName($movie->getGenre()->getName());
            $moviesResponse[] = $movieResponse;
        }
        return $moviesResponse;
    }

    public function getMovie(int $id): GetMovieDetailResponse
    {
        $movie = $this->movieRepository->findById($id);
        if (!$movie) {
            throw new ServiceException(['Movie with id ' . $id . ' not found']);
        }

        $genreResponse = (new GetMovieDetailGenreResponse())
            ->setId($movie->getGenre()->getId())
            ->setName($movie->getGenre()->getName());

        $showtimesResponse = [];
        foreach ($movie->getShowtimes() as $showtime) {
            $showtimeResponse = (new GetMovieDetailShowtimeResponse())
                ->setId($showtime->getId())
                ->setDateStart($showtime->getDateStart())
                ->setDateEnd($showtime->getDateEnd())
                ->setTheaterId($showtime->getTheater()->getId())
                ->setTheaterNumber($showtime->getTheater()->getNumber());
            $showtimesResponse[] = $showtimeResponse;
        }

        return (new GetMovieDetailResponse())
            ->setTitle($movie->getTitle())
            ->setDescription($movie->getDescription())
            ->setPosterImage($movie->getPosterImage())
            ->setDuration($movie->getDuration())
            ->setReleaseDate($movie->getReleaseDate())
            ->setYear($movie->getYear())
            ->setGenre($genreResponse)
            ->setShowtimes($showtimesResponse);
    }

    /**
     * @throws ServiceException
     */
    public function createMovie(CreateMovieRequest $request): bool
    {
        $genre = $this->genreRepository->findById($request->getGenreId());
        if (!$genre) {
            throw new ServiceException(['Genre not found']);
        }
        $movie = (new Movie())
            ->setTitle($request->getTitle())
            ->setDescription($request->getDescription())
            ->setPosterImage($request->getPosterImage())
            ->setDuration($request->getDuration())
            ->setReleaseDate($request->getReleaseDate())
            ->setYear($request->getYear())
            ->setGenre($genre);
        $this->movieRepository->save($movie);
        return true;
    }

    /**
     * @throws ServiceException
     */
    public function updateMovie(int $id, UpdateMovieRequest $request): bool
    {
        $entity = $this->movieRepository->findById($id);
        if (!$entity) {
            throw new ServiceException(['Movie with id ' . $id . ' not found']);
        }
        $genre = $this->genreRepository->findById($request->getGenreId());
        if (!$genre) {
            throw new ServiceException(['Genre not found']);
        }
        $entity->setTitle($request->getTitle());
        $entity->setDescription($request->getDescription());
        $entity->setPosterImage($request->getPosterImage());
        $entity->setDuration($request->getDuration());
        $entity->setReleaseDate($request->getReleaseDate());
        $entity->setYear($request->getYear());
        $entity->setGenre($genre);
        $this->movieRepository->save($entity);
        return true;
    }

    public function deleteMovie(int $id): bool
    {
        $movie = $this->movieRepository->findById($id);
        if (!$movie) {
            return false;
        }
        if ($movie->getDeletedAt() !== null) {
            return false;
        }
        $movie->setDeletedAt(new DateTimeImmutable());
        $this->movieRepository->save($movie);
        return true;
    }

}
