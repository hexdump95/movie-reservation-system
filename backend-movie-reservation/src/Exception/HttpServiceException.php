<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class HttpServiceException extends \RuntimeException implements HttpExceptionInterface
{
    private int $statusCode;
    private array $headers;
    private array $details;

    public function __construct(int $statusCode, string $message = '', array $details = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = [];
        $this->details = $details;
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
