<?php

namespace Core\AdminBundle\Menu;

use Core\AdminBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;


class Builder extends ContainerAware
{
    public function admin(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root', array(
            'childrenAttributes'    => array(
                'class'             => 'navigation',
            ),
        ));

        $menu->setCurrent($this->container->get('request')->getRequestUri());
        $menu->addChild('Dashboard', array(
            'route' => 'admin_dashboard'
            ))
                ->setAttribute('icon', 'menu-icon fa fa-dashboard');

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureMenuEvent::CONFIGURE,
            new ConfigureMenuEvent($factory, $menu)
        );

        return $menu;
    }
}