<?php

namespace App\Service;

use App\DTO\ReservationDetailResponse;
use App\DTO\ReservationResponse;
use App\DTO\ReservationSeatDetailResponse;
use App\Entity\Book;
use App\Entity\BookStatus;
use App\Repository\BookRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ReservationService
{
    private BookRepository $bookRepository;
    private Security $security;

    public function __construct(BookRepository $bookRepository, Security $security)
    {
        $this->bookRepository = $bookRepository;
        $this->security = $security;
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
            $bookStatus = $this->getLastBookStatus($book)->getName();
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
        $bookStatus = $this->getLastBookStatus($book)->getName();
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

    private function getLastBookStatus(Book $book): ?BookStatus
    {
        return $book->getStatusBook()->filter(function ($statusBook) {
            return $statusBook->getDateTo() === null;
        })->last()->getBookStatus();
    }

}
