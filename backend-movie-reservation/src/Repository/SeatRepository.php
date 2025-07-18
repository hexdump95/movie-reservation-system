<?php

namespace App\Repository;

use App\Entity\Seat;
use App\Enum\BookStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seat>
 */
class SeatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seat::class);
    }

    /**
     * @throws Exception
     */
    public function findByShowtimeId(int $showtimeId): ?array
    {
        $query = "
                select s.id,
                s.column_,
                s.row_,
                s.code,
                case when sq.id is null then false else true end occupied,
                sh1.date_start,
                th.number,
                m.title
                from seat s
                left join theater th on th.id = s.theater_id
                left join showtime sh1 on sh1.theater_id = th.id
                left join movie m on m.id = sh1.movie_id
                left join (
                    select s2.id from seat s2
                    left join ticket t2 on t2.seat_id = s2.id
                    left join book b2 on b2.id = t2.book_id
                    left join status_book sb2 on sb2.book_id = b2.id
                    left join book_status bs2 on bs2.id = sb2.book_status_id
                    left join showtime sh2 on sh2.id = b2.showtime_id
                    where
                        sh2.id = :showtimeId
                        and sb2.date_to is null
                        and bs2.name = :paidSeat
                ) sq on sq.id = s.id
                where
                    sh1.id = :showtimeId
                order by s.row_, s.column_;
                ";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->bindValue('showtimeId', $showtimeId);
        $statement->bindValue('paidSeat', BookStatusEnum::PAID->name);
        $seats = $statement->executeQuery()->fetchAllAssociative();
        return $seats == [] ? null : $seats;
    }

    public function findByIdAndShowtimeIdAndCodeNotEmpty(int $id, int $showtimeId): ?Seat
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.theater', 't')
            ->leftJoin('t.showtimes', 'sh')
            ->andWhere('s.id = :id')
            ->andWhere('s.code <> \'\'')
            ->andWhere('sh.id = :showtimeId')
            ->setParameter('id', $id)
            ->setParameter('showtimeId', $showtimeId)
            ->getQuery()
            ->getOneOrNullResult();

    }

//    /**
//     * @return Seat[] Returns an array of Seat objects
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

//    public function findOneBySomeField($value): ?Seat
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
