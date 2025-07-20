<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    const CREATE = 'create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!str_contains($attribute, ':')) {
            return false;
        }

        $action = explode(':', $attribute, 2)[0];

        return in_array($action, [self::CREATE, self::READ, self::UPDATE, self::DELETE]);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $permissions = $token->getUser()->getPermissions();

        if (in_array($attribute, $permissions)) {
            return true;
        }
        return false;
    }
}
