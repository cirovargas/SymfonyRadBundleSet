<?php

namespace Project\AdminBundle\EventListener;

use JordiLlonch\Bundle\CrudGeneratorBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TesteMenuListener{

    public function __construct(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        
        if($user->hasRole('ROLE_ADMIN_TESTE')){
            $menu = $event->getMenu();

                        $menu->addChild('Teste', array(
                'attributes'    => array(
                    'class'             => 'mm-dropdown mm-dropdown-root',
                    )
                )
            )->setAttribute('icon', 'menu-icon fa fa-th-list');

            $menu['Teste']->setUri('#');
            $menu['Teste']->addChild('List Teste', array('route' => 'teste'));
            if($user->hasRole('ROLE_ADMIN_Teste_NEW')){
                $menu['Teste']->addChild('New Teste', array('route' => 'teste_new'));
            }
                    }
    }
}