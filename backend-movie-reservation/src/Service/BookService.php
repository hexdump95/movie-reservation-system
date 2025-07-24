<?php

namespace App\Service;

use App\DTO\BookSeatResponse;
use App\DTO\ReservationDetailResponse;
use App\DTO\ReservationResponse;
use App\DTO\ReservationSeatDetailResponse;
use App\DTO\ReservedSeat;
use App\DTO\ShowtimeResponse;
use App\DTO\ShowtimeSeatResponse;
use App\Entity\Book;
use App\Entity\StatusBook;
use App\Entity\Ticket;
use App\Enum\BookStatusEnum;
use App\Exception\ServiceException;
use App\Repository\BookRepository;
use App\Repository\BookStatusRepository;
use App\Repository\SeatRepository;
use App\Repository\ShowtimeRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Predis\Client;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BookService
{
    private Security $security;
    private ShowtimeRepository $showtimeRepository;
    private BookRepository $bookRepository;
    private UserRepository $userRepository;
    private SeatRepository $seatRepository;
    private Client $redis;
    private CentrifugoService $centrifugoService;
    private BookStatusRepository $bookStatusRepository;

    public function __construct(Security $security, ShowtimeRepository $showtimeRepository, BookRepository $bookRepository, UserRepository $userRepository, SeatRepository $seatRepository, Client $redis, CentrifugoService $centrifugoService, BookStatusRepository $bookStatusRepository)
    {
        $this->security = $security;
        $this->showtimeRepository = $showtimeRepository;
        $this->bookRepository = $bookRepository;
        $this->userRepository = $userRepository;
        $this->seatRepository = $seatRepository;
        $this->redis = $redis;
        $this->centrifugoService = $centrifugoService;
        $this->bookStatusRepository = $bookStatusRepository;
    }

    /**
     * @throws ServiceException
     * @throws Exception
     */
    public function getShowtimeWithSeats(int $showtimeId): ShowtimeResponse
    {
        $seats = $this->seatRepository->findByShowtimeId($showtimeId);
        if ($seats == null) {
            throw new ServiceException(['Showtime with id ' . $showtimeId . ' not found']);
        }

        $seatsResponse = [];
        foreach ($seats as $seat) {
            $seatResponse = (new ShowtimeSeatResponse())
                ->setId($seat['id'])
                ->setColumn($seat['column_'])
                ->setRow($seat['row_'])
                ->setCode($seat['code'])
                ->setIsOccupied($seat['occupied'])
                ->setIsSelected(false);
            $seatsResponse[$seat['row_'] - 1][] = $seatResponse;
        }

        $showtime = (new ShowtimeResponse())
            ->setMovieTitle($seats[0]['title'])
            ->setTheaterNumber($seats[0]['number'])
            ->setDateStart($seats[0]['date_start'])
            ->setSeats($seatsResponse);


        $userEmail = $this->security->getUser()->getUserIdentifier();

        $this->removeTempSeatsFromUser($showtimeId, $userEmail);

        $seatPattern = 'showtime_' . $showtimeId . ':seat_*';
        $redisKeys = $this->redis->keys($seatPattern);

        $temporaryReservations = [];
        foreach ($redisKeys as $key) {
            if ($this->redis->exists($key)) {
                preg_match('/seat_(\d+)$/', $key, $matches);
                $temporaryReservations[] = [
                    'userEmail' => $this->redis->hget($key, 'userEmail'),
                    'seatId' => (int)($matches[1] ?? 0)
                ];
            }
        }

        $seats = $showtime->getSeats();

        foreach ($temporaryReservations as $tempSeat) {
            array_map(function ($row) use ($tempSeat, $userEmail) {
                return array_map(function ($seat) use ($tempSeat, $userEmail) {
                    if ($seat->getId() === $tempSeat['seatId']) {
                        $seat->setIsOccupied($tempSeat['userEmail'] !== $userEmail);
                        $seat->setIsSelected($tempSeat['userEmail'] === $userEmail);
                    }
                    return $seat;
                }, $row);
            }, $seats);
        }

        $showtime->setSeats($seats);
        return $showtime;
    }

    private function removeTempSeatsFromUser(int $showtimeId, string $userEmail): void
    {
        $userPath = 'showtime_' . $showtimeId . ':' . $userEmail;
        $userPreviousTempSeats = $this->redis->get($userPath);
        if ($userPreviousTempSeats) {
            $userPreviousTempSeats = json_decode($userPreviousTempSeats, true);
            foreach ($userPreviousTempSeats as $userPreviousTempSeat) {
                $seatPath = 'showtime_' . $showtimeId . ':' . 'seat_' . $userPreviousTempSeat;
                $this->redis->hdel($seatPath, ['userEmail']);
            }
            $this->redis->del($userPath);
        }
    }

    /**
     * @throws ServiceException
     */
    public function temporaryBookSeat(int $showtimeId, int $seatId): bool
    {
        $pathSeat = 'showtime_' . $showtimeId . ':' . 'seat_' . $seatId;
        $userEmail = $this->security->getUser()->getUserIdentifier();

        if ($this->redis->hget($pathSeat, 'userEmail') == $userEmail) {
            $this->redis->hdel($pathSeat, ['userEmail']);
            try {
                $this->centrifugoService->changeTemporarySeatStatus($showtimeId, false, $seatId, $userEmail);
                return true;
            } catch (TransportExceptionInterface) {
                throw new ServiceException(['Temporary book seat failed']);
            }
        }

        $this->redis->hsetnx(
            $pathSeat,
            'userEmail',
            $userEmail,
        );

        if ($this->redis->hget($pathSeat, 'userEmail') != $userEmail) {
            return false;
        }

        $this->redis->expire($pathSeat, 60 * 6); // TODO: move expirytime to a variable
        try {
            $this->centrifugoService->changeTemporarySeatStatus($showtimeId, true, $seatId, $userEmail);
            return true;
        } catch (TransportExceptionInterface) {
            throw new ServiceException(['Temporary book seat failed']);
        }

    }

    public function holdSeats(int $showtimeId): array
    {
        $seatPattern = 'showtime_' . $showtimeId . ':seat_*';
        $redisKeys = $this->redis->keys($seatPattern);
        $userEmail = $this->security->getUser()->getUserIdentifier();
        $usernamePattern = 'showtime_' . $showtimeId . ':' . $userEmail;

        $seats = [];
        if (count($redisKeys) !== 0) {
            foreach ($redisKeys as $key) {
                if ($this->redis->exists($key)) {
                    if ($this->redis->hget($key, 'userEmail') === $userEmail) {
                        preg_match('/seat_(\d+)$/', $key, $matches);
                        $seats[] = (int)($matches[1] ?? 0);
                        $this->redis->expire($key, 60 * 11);
                    }
                }
            }
            $this->redis->set($usernamePattern, json_encode($seats), 'EX', 60 * 11);
        }
        return $seats;
    }

    /**
     * @throws ServiceException
     */
    public function buySeats(int $showtimeId): BookSeatResponse
    {
        $showtime = $this->showtimeRepository->findOneById($showtimeId);

        if ($showtime === null)
            throw new ServiceException(['Showtime with id ' . $showtimeId . ' not found']);

        $userEmail = $this->security->getUser()->getUserIdentifier();

        $userPath = 'showtime_' . $showtimeId . ':' . $userEmail;
        $bookRequestIds = $this->redis->get($userPath);
        if ($bookRequestIds) {
            $bookRequestIds = json_decode($bookRequestIds, true);
        }

        $user = $this->userRepository->findByEmail($userEmail);

        $bookStatusPaid = $this->bookStatusRepository->findByName(BookStatusEnum::PAID->name);

        $statusBook = (new StatusBook())
            ->setBookStatus($bookStatusPaid);

        $book = (new Book())
            ->setUser($user)
            ->setShowtime($showtime);
        $book->addStatusBook($statusBook);

        $errors = [];
        foreach ($bookRequestIds as $bookRequestId) {
            $seat = $this->seatRepository->findByIdAndShowtimeIdAndCodeNotEmpty(
                $bookRequestId,
                $showtime->getId()
            );
            if ($seat === null) {
                $errors[] = "Seat with id=" . $bookRequestId . " not found";
                continue;
            }

            $exists = $seat->getTickets()->exists(function ($key, $ticket) use ($showtime) {
                $book = $ticket->getBook();
                $lastStatusBook = $book->getStatusBook()->filter(function ($statusBook) {
                    return $statusBook->getDateTo() === null;
                })->last();
                return $book->getShowtime() === $showtime && $lastStatusBook->getBookStatus()->getName() === BookStatusEnum::PAID->name;
            });

            if ($exists) {
                $errors[] = "Seat with id=" . $bookRequestId . " is already occupied";
                continue;
            }

            $ticket = (new Ticket())
                ->setSeat($seat)
                ->setPrice(1.00);
            $book->addTicket($ticket);
        }

        if (count($errors) > 0)
            throw new ServiceException($errors);

        $book = $this->bookRepository->save($book);
        $this->removeTempSeatsFromUser($showtimeId, $userEmail);

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


    public function getReservations(int $page): array
    {
        $userEmail = $this->security->getUser()->getUserIdentifier();
        return $this->bookRepository->findAllByUserEmail($userEmail, $page, 15);
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

        if ($this->isPastShowtime($book)) {
            return false;
        }

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

    private function isPastShowtime(Book $book): bool
    {
        return $book->getShowtime()->getDateStart() < new \DateTime();
    }

}
