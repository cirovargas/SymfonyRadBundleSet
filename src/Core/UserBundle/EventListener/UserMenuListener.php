<?php

namespace Core\UserBundle\EventListener;

use Core\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class UserMenuListener
{
    
    public function __construct(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $menu = $event->getMenu();
        if($user->hasRole('ROLE_ADMIN_USERS') || $user->hasRole('ROLE_ADMIN_USERGROUPS')){
                    $menu->addChild('Usuários', array(
                'attributes'    => array(
                    'class'             => 'mm-dropdown mm-dropdown-root',
                    )
                )
            )->setAttribute('icon', 'menu-icon fa fa-users');

            $menu['Usuários']->setUri('#');
        }
        
        if($user->hasRole('ROLE_ADMIN_USERS'))
            $menu['Usuários']->addChild('Lista de usuários', array('route' => 'admin_users'));
        
        if($user->hasRole('ROLE_ADMIN_USERS') && $user->hasRole('ROLE_ADMIN_USERS_ADD'))   
            $menu['Usuários']->addChild('Adicionar usuário', array('route' => 'admin_users_new'));
        
        if($user->hasRole('ROLE_ADMIN_USERGROUPS'))
            $menu['Usuários']->addChild('Grupos', array('route' => 'fos_user_group_list'));
        
        if($user->hasRole('ROLE_ADMIN_USERGROUPS') && $user->hasRole('ROLE_ADMIN_USERGROUPS_ADD'))
            $menu['Usuários']->addChild('Adicionar grupo', array('route' => 'fos_user_group_new'));
        
    }
}