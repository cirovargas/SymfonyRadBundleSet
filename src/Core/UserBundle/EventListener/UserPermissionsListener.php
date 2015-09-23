<?php

namespace Core\UserBundle\EventListener;

use Core\UserBundle\Event\ConfigurePermissionsEvent;

class UserPermissionsListener
{
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function addModulePermissions(ConfigurePermissionsEvent $event)
    {
        $event->addRootNode('users',array(
            'label' => 'Administrar usuários',
            'role' => 'ROLE_ADMIN_USERS',
            'children' => array(
                array(
                    'label' => 'Adicionar usuários',
                    'role' => 'ROLE_ADMIN_USERS_ADD'
                ),
                array(
                    'label' => 'Visualizar usuários',
                    'role' => 'ROLE_ADMIN_USERS_SHOW'
                ),
                array(
                    'label' => 'Editar usuários',
                    'role' => 'ROLE_ADMIN_USERS_EDIT'
                )
            )
        ));
        $event->addRootNode('groups',array(
            'label' => 'Administrar grupos de usuários',
            'role' => 'ROLE_ADMIN_USERGROUPS',
            'children' => array(
                array(
                    'label' => 'Adicionar grupo',
                    'role' => 'ROLE_ADMIN_USERGROUPS_ADD'
                ),
                array(
                    'label' => 'Visualizar grupo',
                    'role' => 'ROLE_ADMIN_USERGROUPS_SHOW'
                ),
                array(
                    'label' => 'Editar grupo',
                    'role' => 'ROLE_ADMIN_USERGROUPS_EDIT'
                ),
                array(
                    'label' => 'Sincronizar grupos ao LDAP',
                    'role' => 'ROLE_ADMIN_USERGROUPS_SINCRONIZE'
                )
            )
        ));
        $event->addRootNode('switch',array(
            'label' => 'Personificar usuários',
            'role' => 'ROLE_ALLOWED_TO_SWITCH'
        ));
    }
}