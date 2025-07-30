<?php

namespace App\Service;

use App\DTO\CreateTheaterRequest;
use App\DTO\GetTheaterDetailResponse;
use App\DTO\GetTheaterResponse;
use App\Entity\Seat;
use App\Entity\Theater;
use App\Exception\ServiceException;
use App\Repository\TheaterRepository;
use DateTimeImmutable;

class TheaterService
{
    private TheaterRepository $theaterRepository;

    public function __construct(TheaterRepository $theaterRepository)
    {
        $this->theaterRepository = $theaterRepository;
    }

    public function getTheaters(): array
    {
        $theatersResponse = [];
        $theaters = $this->theaterRepository->findAllWhereDeletedAtIsNull();
        foreach ($theaters as $theater) {
            $theaterResponse = (new GetTheaterResponse())
                ->setId($theater->getId())
                ->setNumber($theater->getNumber());
            $theatersResponse[] = $theaterResponse;
        }
        return $theatersResponse;
    }

    public function getTheater(int $id): GetTheaterDetailResponse
    {
        $theater = $this->theaterRepository->findById($id);
        if (!$theater) {
            throw new ServiceException(["Theater with id $id not found"]);
        }

        $seatsResponse = [];
        foreach ($theater->getSeats() as $seat) {
            $seatResponse = $seat->getCode() !== '';
            $seatsResponse[$seat->getRow() - 1][] = $seatResponse;
        }

        return (new GetTheaterDetailResponse())
            ->setNumber($theater->getNumber())
            ->setSeats($seatsResponse);
    }

    public function getUnavailableDates(int $id): array
    {
        return $this->theaterRepository->findUnavailableDates($id);
    }

    public function createTheater(CreateTheaterRequest $theaterDto): bool
    {
        if ($this->theaterRepository->existsByNumber($theaterDto->getNumber()))
            return false;

        if (count($theaterDto->getSeatsGrid()) > 26 || count($theaterDto->getSeatsGrid()[0]) > 26)
            return false;

        $theater = (new Theater())
            ->setNumber($theaterDto->getNumber());
        $alphabet = range('A', 'Z');

        foreach ($theaterDto->getSeatsGrid() as $rowIndex => $row) {
            $rowLetter = $alphabet[$rowIndex];
            foreach ($row as $colIndex => $isSeatAvailable) {
                $seat = new Seat();
                $seat->setRow($rowIndex + 1);
                $seat->setColumn($colIndex + 1);

                if ($isSeatAvailable) {
                    $seatCode = $rowLetter . ($colIndex + 1);
                    $seat->setCode($seatCode);
                } else {
                    $seat->setCode("");
                }

                $theater->addSeat($seat);
            }
        }
        $this->theaterRepository->save($theater);

        return true;
    }

    public function deleteTheater(int $id): bool
    {
        $theater = $this->theaterRepository->findById($id);
        if (!$theater) {
            return false;
        }
        if ($theater->getShowtimes()->count() === 0) {
            $this->theaterRepository->delete($theater);
            return true;
        }
        if ($theater->getDeletedAt() !== null) {
            return false;
        } else {
            $theater->setDeletedAt(new DateTimeImmutable());
            $this->theaterRepository->save($theater);
            return true;
        }
    }

}
