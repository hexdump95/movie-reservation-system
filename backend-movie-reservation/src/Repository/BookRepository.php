<?php

namespace App\Repository;

use App\DTO\ReservationResponse;
use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function save(Book $entity): Book
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        return $entity;
    }

    public function findAllByUserEmail(string $email, int $currentPage, int $limit): array
    {
        $count = $this->createQueryBuilder('b')
            ->select('count(b.id)')
            ->join('b.user_', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleScalarResult();

        $offset = ($currentPage - 1) * $limit;

        $books = $this->createQueryBuilder('b')
            ->join('b.user_', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->orderBy('b.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $totalPages = ceil($count / $limit);
        $currentPage = max(1, min($currentPage, $totalPages));

        $booksDto = [];
        foreach ($books as $book) {
            $bookId = $book->getId();
            $bookTotalPrice = $book->getTotalPrice();
            $movieTitle = $book->getShowtime()->getMovie()->getTitle();
            $bookStatus = $book->getStatusBook()->filter(function ($statusBook) {
                return $statusBook->getDateTo() === null;
            })->last()->getBookStatus()->getName();
            $bookCreatedAt = $book->getCreatedAt();
            $showtimeDateStart = $book->getShowtime()->getDateStart();
            $theaterNumber = $book->getShowtime()->getTheater()->getNumber();
            $totalSeats = $book->getTickets()->count();

            $bookDto = (new ReservationResponse())
                ->setBookId($bookId)
                ->setBookTotalPrice($bookTotalPrice)
                ->setMovieTitle($movieTitle)
                ->setBookStatus($bookStatus)
                ->setBookCreatedAt($bookCreatedAt)
                ->setShowtimeDateStart($showtimeDateStart)
                ->setTheaterNumber($theaterNumber)
                ->setTotalSeats($totalSeats);

            $booksDto[] = $bookDto;
        }

        return [
            'totalPages' => $totalPages,
            'currentPage' => $currentPage,
            'pageSize' => $limit,
            'hasPreviousPage' => $currentPage > 1,
            'hasNextPage' => $currentPage < $totalPages,
            'data' => $booksDto,
        ];
    }

    public function findOneByIdAndUserEmail(int $id, string $email): ?Book
    {
        return $this->createQueryBuilder('b')
            ->join('b.user_', 'u')
            ->where('b.id = :id')
            ->andWhere('u.email = :email')
            ->setParameter('id', $id)
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
