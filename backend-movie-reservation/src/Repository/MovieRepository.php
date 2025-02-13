<?php

namespace App\Repository;

use App\DTO\UpcomingMovieResponse;
use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }
    public function findUpcomingMovies($currentPage, $limit): array
    {
        $count = $this->createQueryBuilder('m')
            ->select('count(m.id)')
            ->leftJoin(
                'm.showtimes',
                's',
                'WITH',
                's.dateStart = (SELECT MIN(s2.dateStart) FROM App\Entity\Showtime s2 WHERE s2.movie = m.id AND s2.dateStart >= :now)'
            )
            ->where('(m.releaseDate >= :now or s.dateStart >= :now) and m.deletedAt is null')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();

        $offset = ($currentPage - 1) * $limit;

        $movies = $this->createQueryBuilder('m')
            ->leftJoin(
                'm.showtimes',
                's',
                'WITH',
                's.dateStart = (SELECT MIN(s2.dateStart) FROM App\Entity\Showtime s2 WHERE s2.movie = m.id AND s2.dateStart >= :now)'
            )
            ->where('(m.releaseDate >= :now or s.dateStart >= :now) and m.deletedAt is null')
            ->setParameter('now', new \DateTime())
            ->orderBy('s.dateStart', 'ASC')
            ->addOrderBy('m.releaseDate', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalPages = ceil($count / $limit);
        $currentPage = max(1, min($currentPage, $totalPages));

        $moviesDto = [];
        foreach ($movies as $movie) {
            $hasShowTime = !$movie->getShowtimes()->isEmpty() && $movie->getShowtimes()->first()->getDateStart() > new \DateTime();
            $movieDto = (new UpcomingMovieResponse())
                ->setId($movie->getId())
                ->setTitle($movie->getTitle())
                ->setPosterImage($movie->getPosterImage())
                ->setHasShowtime($hasShowTime);
            $moviesDto[] = $movieDto;
        }

        return [
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'pageSize' => $limit,
            'hasPreviousPage' => $currentPage > 1,
            'hasNextPage' => $currentPage < $totalPages,
            'data' => $moviesDto,
        ];
    }

    public function getMovieDetail(int $id) {
        return $this->createQueryBuilder('m')
            ->join('m.showtimes', 's')
            ->where('m.id = :id and m.deletedAt is null')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Movie[] Returns an array of Movie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Movie
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
