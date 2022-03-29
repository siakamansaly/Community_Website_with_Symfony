<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickVoter extends Voter
{
    public const TRICK_EDIT = 'TRICK_EDIT';
    public const TRICK_DELETE = 'TRICK_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $trick): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::TRICK_EDIT, self::TRICK_DELETE])
            && $trick instanceof \App\Entity\Trick;
    }

    protected function voteOnAttribute(string $attribute, $trick, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Check trick owner
        if (null === $trick->getUser()) {
            return false;
        }

        // Check if user is admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::TRICK_EDIT:
                // logic to determine if the user can TRICK_EDIT
                // return true or false
                return $this->canEdit($trick, $user);
                break;
            case self::TRICK_DELETE:
                // logic to determine if the user can TRICK_DELETE
                // return true or false
                return $this->canDelete($trick, $user);
                break;
        }

        return false;
    }

    private function canEdit(Trick $trick, User $user)
    {
        return $user === $trick->getUser();
    }

    private function canDelete(Trick $trick, User $user)
    {
        return $user === $trick->getUser();
    }
}
