<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ServiceException extends \Exception
{
    private array $details;

    public function __construct(array $details)
    {
        parent::__construct('Not Found', Response::HTTP_NOT_FOUND);
        $this->details = $details;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(array $details): static
    {
        $this->details = $details;
        return $this;
    }
}
