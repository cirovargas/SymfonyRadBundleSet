<?php

namespace Core\UserBundle\EventListener;

use JordiLlonch\Bundle\CrudGeneratorBundle\Event\ConfigureMenuEvent;

class UserMenuListener
{
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        
                $menu->addChild('Users', array(
            'attributes'    => array(
                'class'             => 'mm-dropdown mm-dropdown-root',
                )
            )
        )->setAttribute('icon', 'menu-icon fa fa-users');
        
        $menu['Users']->setUri('#');
        $menu['Users']->addChild('User List', array('route' => 'admin_users'));
        $menu['Users']->addChild('New User', array('route' => 'admin_users_new'));
        $menu['Users']->addChild('Group List', array('route' => 'fos_user_group_list'));
        $menu['Users']->addChild('New Group', array('route' => 'fos_user_group_new'));
            }
}