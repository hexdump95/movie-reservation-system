<?php

namespace App\Service;

use App\DTO\AddShowtimeRequest;
use App\DTO\AddShowtimeResponse;
use App\DTO\AvailableShowtimeResponse;
use App\DTO\CreateMovieRequest;
use App\DTO\GetMovieDetailGenreResponse;
use App\DTO\GetMovieDetailResponse;
use App\DTO\GetMovieDetailShowtimeResponse;
use App\DTO\GetMovieResponse;
use App\DTO\GetShowtimeResponse;
use App\DTO\MovieDetailResponse;
use App\DTO\UpdateMovieRequest;
use App\DTO\UpdateMovieResponse;
use App\Entity\Movie;
use App\Entity\Showtime;
use App\Exception\ServiceException;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Repository\ShowtimeRepository;
use App\Repository\TheaterRepository;
use DateTimeImmutable;

class MovieService
{
    private MovieRepository $movieRepository;
    private GenreRepository $genreRepository;
    private ShowtimeRepository $showtimesRepository;
    private TheaterRepository $theaterRepository;
    private ShowtimeRepository $showtimeRepository;

    public function __construct(MovieRepository $movieRepository, GenreRepository $genreRepository, ShowtimeRepository $showtimesRepository, TheaterRepository $theaterRepository, ShowtimeRepository $showtimeRepository)
    {
        $this->movieRepository = $movieRepository;
        $this->genreRepository = $genreRepository;
        $this->showtimesRepository = $showtimesRepository;
        $this->theaterRepository = $theaterRepository;
        $this->showtimeRepository = $showtimeRepository;
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
        $movies = $this->movieRepository->findAllWhereDeletedAtIsNullOrderByIdDesc();
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
    public function updateMovie(int $id, UpdateMovieRequest $request): UpdateMovieResponse
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
        $entity = $this->movieRepository->save($entity);

        return (new UpdateMovieResponse())
            ->setId($entity->getId())
            ->setTitle($entity->getTitle())
            ->setYear($entity->getYear())
            ->setGenreName($entity->getGenre()->getName());
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

    public function getShowtimes(int $movieId): array
    {
        $showtimes = $this->showtimesRepository->findAllByMovieId($movieId);
        $showtimesResponse = [];
        foreach ($showtimes as $showtime) {
            $showtimeResponse = (new GetShowtimeResponse())
                ->setId($showtime->getId())
                ->setDateStart($showtime->getDateStart())
                ->setDateEnd($showtime->getDateEnd())
                ->setTheaterId($showtime->getTheater()->getId())
                ->setTheaterNumber($showtime->getTheater()->getNumber());
            $showtimesResponse[] = $showtimeResponse;
        }
        return $showtimesResponse;
    }

    public function addShowtime(int $id, AddShowtimeRequest $showtimeRequest): AddShowtimeResponse
    {
        $showtimeDateStart = $showtimeRequest->getDateStart();

        $movie = $this->movieRepository->findById($id);
        if (!$movie) {
            throw new ServiceException(['Movie not found']);
        }

        $showtimeDateEnd = (clone $showtimeDateStart)->add(new \DateInterval('PT' . ($movie->getDuration() + 30) . 'M'));
        $isAvailable = $this->showtimeRepository->checkAvailableDateByTheaterId($showtimeDateStart, $showtimeDateEnd, $showtimeRequest->getTheaterId());
        if (!$isAvailable) {
            throw new ServiceException([]);
        }

        $theater = $this->theaterRepository->findById($showtimeRequest->getTheaterId());
        if (!$theater) {
            throw new ServiceException(['Theater not found']);
        }
        $showtime = (new Showtime())
            ->setMovie($movie)
            ->setDateStart($showtimeDateStart)
            ->setDateEnd($showtimeDateEnd)
            ->setTheater($theater);

        $movie->getShowtimes()->add($showtime);
        $this->movieRepository->save($movie);

        return (new AddShowtimeResponse())
            ->setId($showtime->getId())
            ->setDateStart($showtimeDateStart)
            ->setDateEnd($showtimeDateEnd)
            ->setTheaterNumber($theater->getNumber())
            ->setTheaterId($theater->getId());
    }

    public function removeShowtime(int $showtimeId): bool
    {
        $showtime = $this->showtimeRepository->findOneById($showtimeId);
        if (!$showtime) {
            return false;
        }
        if ($showtime->getBooks()->count() === 0) {
            $this->showtimeRepository->delete($showtime);
            return true;
        } else {
            return false;
        }
    }

}
