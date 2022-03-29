<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const USER_EDIT = 'USER_EDIT';
    public const USER_EDIT_ROLE = 'USER_EDIT_ROLE';
    public const USER_DELETE = 'USER_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $userVoter): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::USER_EDIT, self::USER_EDIT_ROLE, self::USER_DELETE])
            && $userVoter instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $userVoter, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Check if user is admin
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::USER_EDIT:
                // logic to determine if the user can USER_EDIT
                // return true or false
                return $this->canEdit($userVoter, $user);
                break;
            case self::USER_EDIT_ROLE:
                // logic to determine if the user can USER_DELETE
                // return true or false
                return $this->canEditRole();
                break;
            case self::USER_DELETE:
                // logic to determine if the user can USER_DELETE
                // return true or false
                return $this->canDelete();
                break;
        }

        return false;
    }

    private function canEdit(User $userVoter, User $user)
    {
        return $user === $userVoter;
    }

    private function canEditRole()
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
    }

    private function canDelete()
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        return false;
    }
}
