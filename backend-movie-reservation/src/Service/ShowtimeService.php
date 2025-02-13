<?php

namespace App\Service;

use App\DTO\ShowtimeResponse;
use App\DTO\ShowtimeSeatResponse;
use App\Exception\ServiceException;
use App\Repository\SeatRepository;
use Doctrine\DBAL\Exception;

class ShowtimeService
{
    private SeatRepository $seatRepository;

    public function __construct(SeatRepository $seatRepository)
    {
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

}