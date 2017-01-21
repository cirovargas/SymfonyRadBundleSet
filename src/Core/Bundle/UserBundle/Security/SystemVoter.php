<?php

namespace Core\Bundle\UserBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Core\Bundle\UserBundle\Entity\User;

class SystemVoter extends Voter
{
    private $userInstancia;
    
    public function __construct($userInstancia) {
        $this->userInstancia = $userInstancia;
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
        
        if('ROLE_PREVIOUS_ADMIN' == $attribute){
            return $this->userInstancia->isGranted($attribute);
        }
        
        if ($user->hasRole('ROLE_SUPERADMIN')) {
            return true;
        }
        
        if ($this->userInstancia->isGranted($attribute)){
            return true;
        }
        

        return false;
    }
}