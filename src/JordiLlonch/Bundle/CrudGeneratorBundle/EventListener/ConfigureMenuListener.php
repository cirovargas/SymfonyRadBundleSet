<?php

namespace JordiLlonch\Bundle\CrudGeneratorBundle\EventListener;

use JordiLlonch\Bundle\CrudGeneratorBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

    }
}