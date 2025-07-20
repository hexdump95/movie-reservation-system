<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Bundle\SecurityBundle\Security;

class JWTCreatedListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $permissions = $this->security->getUser()->getPermissions();
            $payload = $event->getData();
            $payload['permissions'] = $permissions;

            $event->setData($payload);
        }
    }

}
