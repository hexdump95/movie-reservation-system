<?php

namespace App\Controller;

use App\Exception\HttpServiceException;
use App\Exception\ServiceException;
use App\Service\ShowtimeService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/showtimes')]
class ShowtimeController extends AbstractController
{
    private ShowtimeService $showtimeService;
    private SerializerInterface $serializer;

    public function __construct(ShowtimeService $showtimeService, SerializerInterface $serializer)
    {
        $this->showtimeService = $showtimeService;
        $this->serializer = $serializer;
    }

    /**
     * @throws Exception
     */
    #[Route('/{id}', name: 'getShowtime')]
    public function getShowtimeWithSeats($id): JsonResponse
    {
        try {
            $seats = $this->showtimeService->getShowtimeWithSeats($id);

            return new JsonResponse(
                $this->serializer->normalize($seats),
                Response::HTTP_OK
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }
}