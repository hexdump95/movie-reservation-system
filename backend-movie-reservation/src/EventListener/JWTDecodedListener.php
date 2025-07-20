<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTDecodedListener
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function onJWTDecoded(JWTDecodedEvent $event): void
    {
        $payload = $event->getPayload();

        $request = $this->requestStack->getCurrentRequest();
        $request?->attributes->set('jwt_payload', $payload);
    }

}