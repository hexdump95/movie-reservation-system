<?php

namespace App\Controller;

use App\DTO\CreateTheaterRequest;
use App\Exception\HttpServiceException;
use App\Exception\ServiceException;
use App\Service\TheaterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[Route('', name: 'get_theaters', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getTheaters(): JsonResponse
    {
        $theaters = $this->theaterService->getTheaters();
        return new JsonResponse(
            $this->serializer->normalize($theaters),
            Response::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'get_theater', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getTheater(int $id): JsonResponse
    {
        try {
            $theater = $this->theaterService->getTheater($id);
            return new JsonResponse(
                $this->serializer->normalize($theater),
                Response::HTTP_OK
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }

    #[Route('/{id}/unavailable-dates', name: 'get_unavailable_dates', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getUnavailableDates(int $id): JsonResponse
    {
        $dates = $this->theaterService->getUnavailableDates($id);
        return new JsonResponse(
            $this->serializer->normalize($dates),
            Response::HTTP_OK
        );
    }

    #[Route('', name: 'create_theater', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
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

    #[Route('/{id}', name: 'delete_theater', methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteTheater(int $id): JsonResponse
    {
        $response = $this->theaterService->deleteTheater($id);
        return new JsonResponse(
            ['success' => $response],
            Response::HTTP_OK
        );
    }


}
