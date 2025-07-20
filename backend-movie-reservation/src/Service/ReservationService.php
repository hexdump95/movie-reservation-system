<?php

namespace App\Service;

use App\DTO\ReservationDetailResponse;
use App\DTO\ReservationResponse;
use App\DTO\ReservationSeatDetailResponse;
use App\Entity\Book;
use App\Entity\StatusBook;
use App\Enum\BookStatusEnum;
use App\Repository\BookRepository;
use App\Repository\BookStatusRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ReservationService
{
    private Security $security;
    private BookRepository $bookRepository;
    private BookStatusRepository $bookStatusRepository;

    public function __construct(Security $security, BookRepository $bookRepository, BookStatusRepository $bookStatusRepository)
    {
        $this->security = $security;
        $this->bookRepository = $bookRepository;
        $this->bookStatusRepository = $bookStatusRepository;
    }

    public function getReservations(): array
    {
        $userEmail = $this->security->getUser()->getUserIdentifier();
        $reservations = $this->bookRepository->findAllByUserEmail($userEmail);

        $reservationsResponse = [];
        foreach ($reservations as $book) {
            $bookId = $book->getId();
            $bookTotalPrice = $book->getTotalPrice();
            $movieTitle = $book->getShowtime()->getMovie()->getTitle();
            $bookStatus = $this->getLastStatusBook($book)->getBookStatus()->getName();
            $bookCreatedAt = $book->getCreatedAt();
            $showtimeDateStart = $book->getShowtime()->getDateStart();
            $theaterNumber = $book->getShowtime()->getTheater()->getNumber();
            $totalSeats = $book->getTickets()->count();

            $reservationResponse = (new ReservationResponse())
                ->setBookId($bookId)
                ->setBookTotalPrice($bookTotalPrice)
                ->setMovieTitle($movieTitle)
                ->setBookStatus($bookStatus)
                ->setBookCreatedAt($bookCreatedAt)
                ->setShowtimeDateStart($showtimeDateStart)
                ->setTheaterNumber($theaterNumber)
                ->setTotalSeats($totalSeats);

            $reservationsResponse[] = $reservationResponse;
        }

        return $reservationsResponse;
    }

    public function getReservationById($bookId): ReservationDetailResponse
    {
        $userEmail = $this->security->getUser()->getUserIdentifier();
        $book = $this->bookRepository->findOneByIdAndUserEmail($bookId, $userEmail);

        $bookTotalPrice = $book->getTotalPrice();
        $bookStatus = $this->getLastStatusBook($book)->getBookStatus()->getName();
        $bookCreatedAt = $book->getCreatedAt();
        $showtimeDateStart = $book->getShowtime()->getDateStart();
        $movieTitle = $book->getShowtime()->getMovie()->getTitle();
        $theaterNumber = $book->getShowtime()->getTheater()->getNumber();

        $seats = [];
        foreach ($book->getTickets() as $ticket) {
            $seat = $ticket->getSeat();
            $seatResponse = (new ReservationSeatDetailResponse())
                ->setPrice($ticket->getPrice())
                ->setSeatCode($seat->getCode());
            $seats[] = $seatResponse;
        }

        return (new ReservationDetailResponse())
            ->setBookId($bookId)
            ->setBookTotalPrice($bookTotalPrice)
            ->setBookStatus($bookStatus)
            ->setBookCreatedAt($bookCreatedAt)
            ->setShowtimeDateStart($showtimeDateStart)
            ->setMovieTitle($movieTitle)
            ->setTheaterNumber($theaterNumber)
            ->setSeats($seats);
    }

    public function cancelReservation($bookId): bool
    {
        $userEmail = $this->security->getUser()->getUserIdentifier();
        $book = $this->bookRepository->findOneByIdAndUserEmail($bookId, $userEmail);

        $lastBookStatus = $this->getLastStatusBook($book);
        if ($lastBookStatus->getBookStatus()->getName() !== BookStatusEnum::PAID->name)
            return false;

        $lastBookStatus->setDateTo(new \DateTimeImmutable());

        $bookStatusCanceled = $this->bookStatusRepository->findByName(BookStatusEnum::CANCELED->name);

        $newStatusBook = (new StatusBook())
            ->setBookStatus($bookStatusCanceled)
            ->setDateFrom(new \DateTimeImmutable());

        $book->addStatusBook($newStatusBook);

        return $this->bookRepository->save($book) !== null;
    }

    private function getLastStatusBook(Book $book): ?StatusBook
    {
        return $book->getStatusBook()->filter(function ($statusBook) {
            return $statusBook->getDateTo() === null;
        })->last();
    }

}
