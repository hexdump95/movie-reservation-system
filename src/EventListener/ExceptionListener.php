<?php

namespace App\EventListener;

use App\Exception\HttpServiceException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 400;
        $response = new JsonResponse([
            'code' => $statusCode,
            'message' => $exception->getMessage(),
            'details' => $exception instanceof HttpServiceException ? $exception->getDetails() : [],
        ], $statusCode);
        $event->setResponse($response);
    }
}
