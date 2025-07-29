<?php

namespace App\Controller;

use App\Service\GenreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/genres')]
class GenreController extends AbstractController
{
    private GenreService $genreService;
    private SerializerInterface $serializer;

    public function __construct(GenreService $genreService, SerializerInterface $serializer)
    {
        $this->genreService = $genreService;
        $this->serializer = $serializer;
    }

    #[Route('', name: 'get_genres', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getGenres(): JsonResponse
    {
        $genres = $this->genreService->getGenres();
        return new JsonResponse(
            $this->serializer->normalize($genres),
            Response::HTTP_OK,
        );
    }

}
