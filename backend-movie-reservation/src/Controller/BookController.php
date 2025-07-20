<?php

namespace App\Controller;

use App\Exception\HttpServiceException;
use App\Exception\ServiceException;
use App\Service\BookService;
use App\Service\CentrifugoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/book')]
class BookController extends AbstractController
{
    private BookService $bookService;
    private SerializerInterface $serializer;
    private CentrifugoService $centrifugoService;

    public function __construct(BookService $bookService, SerializerInterface $serializer, CentrifugoService $centrifugoService)
    {
        $this->bookService = $bookService;
        $this->serializer = $serializer;
        $this->centrifugoService = $centrifugoService;
    }

    #[Route('/showtimes/{id}', name: 'getShowtime')]
    public function getShowtimeWithSeats($id): JsonResponse
    {
        try {
            $showtime = $this->bookService->getShowtimeWithSeats($id);

            return new JsonResponse(
                $this->serializer->normalize($showtime),
                Response::HTTP_OK
            );
        } catch (ServiceException $e) {
            throw new HttpServiceException($e->getCode(), $e->getMessage(), $e->getDetails());
        } catch (\Doctrine\DBAL\Exception $e) { // TODO: Use a different exception
            throw new HttpServiceException($e->getCode(), $e->getMessage(), []);
        }
    }

    #[Route('/getCentrifugoToken/{showtimeId}', name: 'getCentrifugoToken', methods: ['GET'])]
    public function getCentrifugoToken(int $showtimeId): JsonResponse
    {
        $userEmail = $this->getUser()->getUserIdentifier();
        $token = $this->centrifugoService->generateConnectionToken($userEmail);

        return new JsonResponse(
            [
                'ws_token' => $token,
                'channel' => "showtime_$showtimeId",
            ]);
    }

    #[Route('/showtimes/{showtimeId}/seats/{seatId}', name: 'updateSeatStatus', methods: ['PUT'])]
    public function temporaryBookSeat(int $showtimeId, int $seatId): JsonResponse
    {
        try {
            $bookSeat = $this->bookService->temporaryBookSeat($showtimeId, $seatId);
            return new JsonResponse(['success' => $bookSeat]);
        } catch (ServiceException $e) {
            throw new HttpServiceException($e->getCode(), $e->getMessage(), $e->getDetails());
        }
    }

    #[Route('/showtimes/{showtimeId}/hold', name: 'holdSeats', methods: ['POST'])]
    public function holdSeats(int $showtimeId): JsonResponse
    {
        try {
            $seats = $this->bookService->holdSeats($showtimeId);
            return new JsonResponse(['seats' => $seats]);
        } catch (ServiceException $e) {
            throw new HttpServiceException($e->getCode(), $e->getMessage(), $e->getDetails());
        }
    }

    #[Route('/showtimes/{showtimeId}/pay', name: 'paySeats', requirements: ['showtimeId' => '\d+'], methods: ['POST'])]
    public function paySeats(int $showtimeId): JsonResponse
    {
        try {
            $bookResponse = $this->bookService->buySeats($showtimeId);
            return new JsonResponse(
                $this->serializer->normalize($bookResponse),
                Response::HTTP_OK
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }
}
