<?php

namespace App\Security\Voter;


use App\Entity\Comment;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const COMMENT_ADD = 'COMMENT_ADD';
    public const COMMENT_DELETE = 'COMMENT_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $comment): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::COMMENT_ADD, self::COMMENT_DELETE])
            && $comment instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute(string $attribute, $comment, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Check if user is admin
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::COMMENT_ADD:
                // logic to determine if the user can COMMENT_ADD
                // return true or false
                return $this->canAdd();
                break;

            case self::COMMENT_DELETE:
                // logic to determine if the user can COMMENT_DELETE
                // return true or false
                return $this->canDelete();
                break;
        }

        return false;
    }

    private function canAdd()
    {
        switch (true) {
            case $this->security->isGranted('ROLE_ADMIN'):
                return true;
                break;
            case $this->security->isGranted('ROLE_USER'):
                return true;
                break;
            default:
                return false;
                break;
        }
    }


    private function canDelete()
    {
        if ($this->security->isGranted('ROLE_ADMIN')) return true;
        return false;
    }
}
