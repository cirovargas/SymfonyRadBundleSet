<?php

namespace Core\Bundle\UserBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Core\Bundle\UserBundle\Entity\User;

class SystemVoter extends Voter
{
    
    public function __construct() {
        
    }

    protected function supports($attribute, $subject)
    {
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($user->hasRole('ROLE_SUPERADMIN')) {
            return true;
        }
        
        

        return false;
    }
}