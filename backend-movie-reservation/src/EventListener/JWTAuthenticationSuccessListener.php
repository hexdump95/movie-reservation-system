<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class JWTAuthenticationSuccessListener
{
    private int $jwtExpiration;

    public function __construct(ContainerBagInterface $containerBag)
    {
        $this->jwtExpiration = $containerBag->get('jwt_expiration');
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $data = [
            'access_token' => $data['token'],
            'expires_in' => $this->jwtExpiration,
            'token_type' => 'Bearer',
        ];

        $event->setData($data);
    }
}
