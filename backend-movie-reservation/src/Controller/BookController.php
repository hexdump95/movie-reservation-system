<?php

namespace App\Controller;

use App\Exception\HttpServiceException;
use App\Exception\ServiceException;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    private BookService $bookService;
    private SerializerInterface $serializer;

    public function __construct(BookService $bookService, SerializerInterface $serializer)
    {
        $this->bookService = $bookService;
        $this->serializer = $serializer;
    }

    #[Route('/api/v1/showtime/{id}/book', name: 'bookSeats', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function bookSeats(int $id, Request $request): JsonResponse
    {
        try {
            $bookRequests = $this->serializer->deserialize($request->getContent(), 'App\DTO\BookSeatRequest[]', 'json');

            $bookResponse = $this->bookService->bookSeats($id, $bookRequests);
            return new JsonResponse(
                $this->serializer->normalize($bookResponse),
                Response::HTTP_OK
            );
        } catch (ServiceException $exception) {
            throw new HttpServiceException($exception->getCode(), $exception->getMessage(), $exception->getDetails());
        }
    }
}
