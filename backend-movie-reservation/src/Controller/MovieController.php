<?php

namespace App\Controller;

use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/movies')]
class MovieController extends AbstractController
{
    private MovieService $movieService;
    private SerializerInterface $serializer;

    public function __construct(MovieService $movieService, SerializerInterface $serializer)
    {
        $this->movieService = $movieService;
        $this->serializer = $serializer;
    }

    #[Route('', name: 'getUpcomingMovies', methods: ['GET'])]
    public function getUpcomingMovies(): Response
    {
        $moviesDto = $this->movieService->getUpcomingMovies();
        return new JsonResponse(
            $this->serializer->normalize($moviesDto),
            Response::HTTP_OK
        );
    }
}
