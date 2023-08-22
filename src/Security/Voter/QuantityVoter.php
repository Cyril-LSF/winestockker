<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class QuantityVoter extends Voter
{
    public const EDIT_BIG = 'QUANTITY_EDIT_BIG';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT_BIG])
            && $subject instanceof \App\Entity\Quantity;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT_BIG:
                return $this->_is_me($subject, $user);
                break;
        }

        return false;
    }

    private function _is_me(mixed $subject, User $user): bool
    {
        return $subject->getBottle()->getAuthor()->getId() === $user->getId();
    }
}
