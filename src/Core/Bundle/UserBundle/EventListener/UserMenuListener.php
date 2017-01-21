<?php

namespace Core\Bundle\UserBundle\EventListener;

use Core\Bundle\AdminBundle\Event\ConfigureMenuEvent;
use Core\Bundle\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;


class UserMenuListener
{
    
    public function __construct(TokenStorage $tokenStorage, AuthorizationChecker $authorizationChecker) {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }
    
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        
        $menu = $event->getMenu();
        if ($user instanceof User) {
            if (
                    $this->authorizationChecker->isGranted('ROLE_ADMIN_USERS_INDEX') || 
                    $this->authorizationChecker->isGranted('ROLE_ADMIN_USERGROUPS')) {
                $menu->addChild('Usuários', array(
                    'attributes' => array(
                        'class' => '',
                    )
                        )
                )->setAttribute('icon', 'px-nav-icon fa fa-users');

                $menu['Usuários']->setUri('#');

                if ($this->authorizationChecker->isGranted('ROLE_ADMIN_USERS')){
                    $menu['Usuários']->addChild('Ativos',array(
                        'route' => 'admin_users',
                        'routeParameters'=> array(
                            'user_filter'=> array(
                                'enabled'=> 'y',
                            ),
                            'filter_action'=>'filter'
                        )
                    ));
                            
                    $menu['Usuários']->addChild('Inativos',array(
                        'route' => 'admin_users',
                        'routeParameters'=> array(
                            'user_filter'=> array(
                                'enabled'=> 'n',
                            ),
                            'filter_action'=>'filter'
                        )
                    ));
                }
                    

//                if ($this->authorizationChecker->isGranted('ROLE_ADMIN_USERS') && $this->authorizationChecker->isGranted('ROLE_ADMIN_USERS_ADD'))
//                    $menu['Usuários']->addChild('Adicionar usuário', array('route' => 'admin_users_new'));

                if ($this->authorizationChecker->isGranted('ROLE_ADMIN_USERGROUPS'))
                    $menu['Usuários']->addChild('Grupos', array('route' => 'fos_user_group_list'));

//                if ($this->authorizationChecker->isGranted('ROLE_ADMIN_USERGROUPS') && $this->authorizationChecker->isGranted('ROLE_ADMIN_USERGROUPS_ADD'))
//                    $menu['Usuários']->addChild('Adicionar grupo', array('route' => 'fos_user_group_new'));
            }
        }
    }
}