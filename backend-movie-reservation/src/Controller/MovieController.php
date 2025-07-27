<?php

namespace App\Controller;

use App\DTO\CreateMovieRequest;
use App\DTO\UpdateMovieRequest;
use App\Exception\HttpServiceException;
use App\Exception\ServiceException;
use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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

    #[Route('/upcoming', name: 'getUpcomingMovies', methods: ['GET'])]
    public function getUpcomingMovies(#[MapQueryParameter] int $page = 1): Response
    {
        $responseDto = $this->movieService->getUpcomingMovies($page);
        return new JsonResponse(
            $this->serializer->normalize($responseDto),
            Response::HTTP_OK
        );
    }

    #[Route('/upcoming/{id}', name: 'getMovieDetail', methods: ['GET'])]
    public function getUpcomingMovieDetail(int $id): Response
    {
        try {
            $movieResponse = $this->movieService->getUpcomingMovieDetail($id);
            return new JsonResponse(
                $this->serializer->normalize($movieResponse),
                Response::HTTP_OK
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }

    #[Route('', name: 'getMovies', methods: ['GET'])]
    #[IsGranted("read:movies")]
    public function getMovies(): JsonResponse
    {
        $movies = $this->movieService->getMovies();
        return new JsonResponse(
            $this->serializer->normalize($movies),
            Response::HTTP_OK
        );
    }

    #[Route('/{id}', name: 'getMovie', methods: ['GET'])]
    #[IsGranted("read:movies")]
    public function getMovie(int $id): JsonResponse
    {
        try {
            $movieResponse = $this->movieService->getMovie($id);
            return new JsonResponse(
                $this->serializer->normalize($movieResponse),
                Response::HTTP_OK
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }

    #[Route('', name: 'createMovie', methods: ['POST'])]
    #[IsGranted("create:movies")]
    public function createMovie(Request $request): JsonResponse
    {
        $request = $this->serializer->deserialize($request->getContent(), CreateMovieRequest::class, 'json');
        try {
            $this->movieService->createMovie($request);
            return new JsonResponse(
                $this->serializer->normalize($request),
                Response::HTTP_CREATED
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }

    #[Route('/{id}', name: 'updateMovie', methods: ['PUT'])]
    #[IsGranted("update:movies")]
    public function updateMovie(int $id, Request $request): JsonResponse
    {
        $movie = $this->serializer->deserialize($request->getContent(), UpdateMovieRequest::class, 'json');
        try {
            $this->movieService->updateMovie($id, $movie);
            return new JsonResponse(
                $this->serializer->normalize($movie),
                Response::HTTP_OK
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }

    #[Route('/{id}', name: 'deleteMovie', methods: ['DELETE'])]
    #[IsGranted("delete:movies")]
    public function deleteMovie(int $id): JsonResponse
    {
        $deleted = $this->movieService->deleteMovie($id);
        if ($deleted)
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        else
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
    }

}
