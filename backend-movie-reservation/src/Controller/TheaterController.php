<?php

namespace App\Controller;

use App\DTO\CreateTheaterRequest;
use App\Service\TheaterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/theaters')]
class TheaterController extends AbstractController
{
    private TheaterService $theaterService;
    private SerializerInterface $serializer;

    public function __construct(TheaterService $theaterService, SerializerInterface $serializer)
    {
        $this->theaterService = $theaterService;
        $this->serializer = $serializer;
    }

    #[Route('', name: 'getTheaters', methods: ['GET'])]
    public function getTheaters(): JsonResponse
    {
        $theaters = $this->theaterService->getTheaters();
        return new JsonResponse(
            $this->serializer->normalize($theaters),
            Response::HTTP_OK
        );
    }

    #[Route('/{id}/unavailable-dates', name: 'getUnavailableDates', methods: ['GET'])]
    public function getUnavailableDates(int $id): JsonResponse
    {
        $dates = $this->theaterService->getUnavailableDates($id);
        return new JsonResponse(
            $this->serializer->normalize($dates),
            Response::HTTP_OK
        );
    }

    #[Route('', name: 'createTheater', methods: ['POST'])]
    public function createTheater(Request $request): JsonResponse
    {
        $createTheaterRequest = $this->serializer->deserialize($request->getContent(), CreateTheaterRequest::class, 'json');

        $created = $this->theaterService->createTheater($createTheaterRequest);
        if ($created) {
            return new JsonResponse(null, Response::HTTP_CREATED);
        } else {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'deleteTheater', methods: ['DELETE'])]
    public function deleteTheater(int $id): JsonResponse
    {
        $response = $this->theaterService->deleteTheater($id);
        return new JsonResponse(
            ['success' => $response],
            Response::HTTP_OK
        );
    }


}
