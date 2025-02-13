<?php

namespace App\Controller;

use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
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
    public function getUpcomingMovies(#[MapQueryParameter] int $page = 1): Response
    {
        $responseDto = $this->movieService->getUpcomingMovies($page);
        return new JsonResponse(
            $this->serializer->normalize($responseDto),
            Response::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'getMovieDetail', methods: ['GET'])]
    public function getMovieDetail(int $id): Response
    {
        $movieResponse = $this->movieService->getMovieDetail($id);
        return new JsonResponse(
            $this->serializer->normalize($movieResponse),
            Response::HTTP_OK
        );
    }

}
