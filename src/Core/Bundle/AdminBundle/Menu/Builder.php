<?php

namespace Core\Bundle\AdminBundle\Menu;

use Core\Bundle\AdminBundle\Event\ConfigureMenuEvent;
use Knp\Menu\MenuFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    public function admin(MenuFactory $factory)
    {
        $menu = $factory->createItem('root', array(
            'allow_safe_labels ' => true,
            'childrenAttributes'    => array(
                'class'             => 'px-nav-content',
            ),
        ));
        
        $menu->setCurrent(
                $this
                    ->container
                    ->get('request_stack')
                    ->getCurrentRequest()
                    ->getRequestUri()
            );
        $menu->addChild('Inicio', array(
            'route' => 'admin_dashboard'
        ))
            ->setAttribute('icon', 'px-nav-icon fa fa-dashboard');

        $this->container->get('event_dispatcher')->dispatch(
            ConfigureMenuEvent::CONFIGURE,
            new ConfigureMenuEvent($factory, $menu)
        );

        return $menu;
    }
}