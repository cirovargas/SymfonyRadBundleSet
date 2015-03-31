<?php

namespace Project\AdminBundle\EventListener;

use JordiLlonch\Bundle\CrudGeneratorBundle\Event\ConfigureMenuEvent;

class TesteMenuListener
{
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        
                $menu->addChild('Teste', array(
            'attributes'    => array(
                'class'             => 'mm-dropdown mm-dropdown-root',
                )
            )
        );
        
        $menu['Teste']->setUri('#');
        $menu['Teste']->addChild('List Teste', array('route' => 'teste'));
        $menu['Teste']->addChild('New Teste', array('route' => 'teste_new'));
            }
}