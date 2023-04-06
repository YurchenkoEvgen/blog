<?php

namespace App\Security\Voter;

use App\Entity\BlogPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BlogPostVoter extends Voter
{
    public const CREATE = 'POST_CREATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE])
            && $subject instanceof BlogPost;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var BlogPost $subject */
        switch ($attribute) {
            case self::CREATE:
                return $user && $user->getDisplayName() == 'user1';
        }

        return false;
    }
}
