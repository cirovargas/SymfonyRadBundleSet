<?php

namespace Core\Bundle\UserBundle\Ldap;

use FR3D\LdapBundle\Hydrator\AbstractHydrator as BaseHydrator;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class UserHydrator extends BaseHydrator
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param UserManagerInterface $userManager
     * @param array $attributeMap
     */
    public function __construct($userManager, array $attributeMap)
    {
        parent::__construct($attributeMap);

        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function createUser()
    {
        $user = $this->userManager->createUser();
        $user->setPassword('');

        if ($user instanceof AdvancedUserInterface) {
            $user->setEnabled(true);
        }

        return $user;
    }
    
    public function getUserManager(){
        return $this->userManager;
    }
}
