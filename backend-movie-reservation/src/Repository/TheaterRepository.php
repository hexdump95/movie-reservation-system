<?php

namespace App\Repository;

use App\Entity\Theater;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<Theater>
 */
class TheaterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theater::class);
    }

    public function save(Theater $entity): Theater
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    public function findById(int $id): ?Theater
    {
        return $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByNumber(int $number): bool
    {
        $entity = $this->createQueryBuilder('u')
            ->where('u.number = :number')
            ->setParameter('number', $number)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $entity !== null;
    }

    public function findAllWhereDeletedAtIsNull()
    {
        return $this->createQueryBuilder('t')
            ->where('t.deletedAt is null')
            ->getQuery()
            ->getResult();
    }

    public function findUnavailableDates(int $id): array
    {
        $query = "
            select sh.date_start \"from\", sh.date_end \"to\" from showtime sh
            left join theater th on th.id = sh.theater_id
            where th.id = :id
            ;";
        $statement = $this->getEntityManager()->getConnection()->prepare($query);
        $statement->bindValue('id', $id);
        return $statement->executeQuery()->fetchAllAssociative();
    }

    public function delete(Theater $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return Theater[] Returns an array of Theater objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Theater
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
