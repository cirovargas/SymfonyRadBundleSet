<?php

namespace Project\AdminBundle\EventListener;

use Core\UserBundle\Event\ConfigurePermissionsEvent;

class TestePermissionsListener
{
    public function addModulePermissions(ConfigurePermissionsEvent $event)
    {
        $event->addRootNode('Teste',array(
            'label' => 'Access Teste',
            'role' => 'ROLE_ADMIN_TESTE',
            'children' => array(
                                array(
                    'label' => 'Add Teste',
                    'role' => 'ROLE_ADMIN_TESTE_NEW'
                ),
                array(
                    'label' => 'Update Teste',
                    'role' => 'ROLE_ADMIN_TESTE_EDIT'
                ),
                array(
                    'label' => 'Delete Teste',
                    'role' => 'ROLE_ADMIN_TESTE_DELETE'
                ),
                                array(
                    'label' => 'Show Teste',
                    'role' => 'ROLE_ADMIN_TESTE_SHOW'
                )
            )
        ));
    }
}