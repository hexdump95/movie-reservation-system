<?php

namespace App\Repository;

use App\Entity\Showtime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Showtime>
 */
class ShowtimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Showtime::class);
    }

    public function findOneById(int $id): ?Showtime
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByMovieId(int $movieId): mixed
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.movie', 'm')
            ->andWhere('m.id = :movieId')
            ->setParameter('movieId', $movieId)
            ->getQuery()
            ->getResult();
    }

    public function checkAvailableDateByTheaterId(\DateTime $dateStart, \DateTime $dateEnd, int $theaterId): bool
    {
        $query = "
        select count(*)
        from showtime sh
        left join theater th on th.id = sh.theater_id
        where
            th.id = :theaterId
            and (
                (:dateStart > sh.date_start and :dateEnd < sh.date_end)
                or
                (:dateEnd > sh.date_start and :dateStart < sh.date_end)
            )
        ;
        ";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->bindValue('dateStart', $dateStart->format('Y-m-d\TH:i:s'));
        $statement->bindValue('dateEnd', $dateEnd->format('Y-m-d\TH:i:s'));
        $statement->bindValue('theaterId', $theaterId);
        return $statement->executeQuery()->fetchOne() === 0;
    }

    public function delete(Showtime $showtime): void
    {
        $this->getEntityManager()->remove($showtime);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return Showtime[] Returns an array of Showtime objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Showtime
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
