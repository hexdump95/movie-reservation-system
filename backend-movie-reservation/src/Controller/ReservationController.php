<?php

namespace App\Controller;

use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/reservations')]
class ReservationController extends AbstractController
{
    private SerializerInterface $serializer;
    private ReservationService $reservationService;

    public function __construct(SerializerInterface $serializer, ReservationService $reservationService)
    {
        $this->serializer = $serializer;
        $this->reservationService = $reservationService;
    }

    #[Route('', name: 'getReservations', methods: ['GET'])]
    public function getReservations(): JsonResponse
    {
        $reservations = $this->reservationService->getReservations();
        return new JsonResponse(
            $this->serializer->normalize($reservations),
            Response::HTTP_OK
        );
    }

    #[Route('/{bookId}', name: 'getReservation', methods: ['GET'])]
    public function getReservation(int $bookId): JsonResponse
    {
        $reservations = $this->reservationService->getReservationById($bookId);
        return new JsonResponse(
            $this->serializer->normalize($reservations),
            Response::HTTP_OK
        );
    }
}
