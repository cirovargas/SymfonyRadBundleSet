<?php

namespace Core\AdLDAPBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use adLDAP\adLDAP as Base;

class Adldap extends Base{
    
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
        
        parent::__construct($container->getParameter('core_ad_ldap'));
    } 
}
