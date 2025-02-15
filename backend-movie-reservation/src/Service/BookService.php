<?php

namespace App\Service;

use App\DTO\BookSeatResponse;
use App\DTO\ReservedSeat;
use App\DTO\ShowtimeResponse;
use App\DTO\ShowtimeSeatResponse;
use App\Entity\Book;
use App\Entity\Ticket;
use App\Exception\ServiceException;
use App\Repository\BookRepository;
use App\Repository\SeatRepository;
use App\Repository\ShowtimeRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\SecurityBundle\Security;

class BookService
{
    private Security $security;
    private ShowtimeRepository $showtimeRepository;
    private BookRepository $bookRepository;
    private UserRepository $userRepository;
    private SeatRepository $seatRepository;

    public function __construct(Security $security, ShowtimeRepository $showtimeRepository, BookRepository $bookRepository, UserRepository $userRepository, SeatRepository $seatRepository)
    {
        $this->security = $security;
        $this->showtimeRepository = $showtimeRepository;
        $this->bookRepository = $bookRepository;
        $this->userRepository = $userRepository;
        $this->seatRepository = $seatRepository;
    }

    /**
     * @throws ServiceException
     * @throws Exception
     */
    public function getShowtimeWithSeats(int $id): ShowtimeResponse
    {
        $seats = $this->seatRepository->findByShowtimeId($id);
        if ($seats == null) {
            throw new ServiceException(['Showtime with id ' . $id . ' not found']);
        }

        $seatsResponse = [];
        foreach ($seats as $seat) {
            $seatResponse = (new ShowtimeSeatResponse())
                ->setId($seat['id'])
                ->setColumn($seat['column_'])
                ->setRow($seat['row_'])
                ->setCode($seat['code'])
                ->setIsOccupied($seat['occupied']);
            $seatsResponse[$seat['row_']-1][] = $seatResponse;
        }

        return (new ShowtimeResponse())
            ->setMovieTitle($seats[0]['title'])
            ->setTheaterNumber($seats[0]['number'])
            ->setDateStart($seats[0]['date_start'])
            ->setSeats($seatsResponse);
    }

    /**
     * @throws ServiceException
     */
    public function bookSeats(int $id, array $bookRequests): BookSeatResponse
    {
        $showtime = $this->showtimeRepository->findOneById($id);

        if ($showtime === null)
            throw new ServiceException(['Showtime with id ' . $id . ' not found']);

        $userEmail = $this->security->getUser()->getUserIdentifier();
        $user = $this->userRepository->findByEmail($userEmail);

        $book = (new Book())
            ->setUser($user)
            ->setShowtime($showtime);

        $errors = [];
        foreach ($bookRequests as $bookRequest) {
            $seat = $this->seatRepository->findByIdAndShowtimeIdAndCodeNotEmpty(
                $bookRequest->getId(),
                $showtime->getId()
            );
            if ($seat === null) {
                $errors[] = "Seat with id=" . $bookRequest->getId() . " not found";
                continue;
            }

            $exists = $seat->getTickets()->exists(function ($key, $value) use ($showtime) {
                return $value->getBook()->getShowtime() === $showtime;
            });
            if ($exists) {
                $errors[] = "Seat with id=" . $bookRequest->getId() . " is already occupied";
                continue;
            }

            $ticket = (new Ticket())
                ->setSeat($seat)
                ->setPrice(rand(1000, 10000));
            $book->addTicket($ticket);
        }

        if (count($errors) > 0)
            throw new ServiceException($errors);

        $book = $this->bookRepository->save($book);

        $ticketsDto = [];
        foreach ($book->getTickets() as $reservedSeat) {
            $ticketDto = (new ReservedSeat())
                ->setId($reservedSeat->getId())
                ->setPrice($reservedSeat->getPrice())
                ->setSeatCode($reservedSeat->getSeat()->getCode());
            $ticketsDto[] = $ticketDto;
        }

        return (new BookSeatResponse())
            ->setId($book->getId())
            ->setUserEmail($userEmail)
            ->setShowtimeDate($book->getShowtime()->getDateStart())
            ->setMovieDuration($book->getShowtime()->getMovie()->getDuration())
            ->setMovieTitle($book->getShowtime()->getMovie()->getTitle())
            ->setTotalPrice($book->getTotalPrice())
            ->setSeats($ticketsDto);
    }

}