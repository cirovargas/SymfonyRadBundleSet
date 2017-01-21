<?php

namespace Core\Bundle\UserBundle\EventListener;

use Core\Bundle\UserBundle\Event\ConfigurePermissionsEvent;

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
                    'label' => 'Listar usuários',
                    'role' => 'ROLE_ADMIN_USERS_INDEX'
                ),
                array(
                    'label' => 'Adicionar usuários',
                    'role' => 'ROLE_ADMIN_USERS_ADD'
                ),
                array(
                    'label' => 'Visualizar usuário',
                    'role' => 'ROLE_ADMIN_USERS_SHOW'
                ),
                array(
                    'label' => 'Editar usuários',
                    'role' => 'ROLE_ADMIN_USERS_EDIT'
                ),
                array(
                    'label' => 'Excluir usuários',
                    'role' => 'ROLE_ADMIN_USERS_DELETE'
                ),
                array(
                    'label' => 'Ativar usuários',
                    'role' => 'ROLE_ADMIN_USERS_ATIVAR'
                ),
                array(
                    'label' => 'Inativar usuários',
                    'role' => 'ROLE_ADMIN_USERS_INATIVAR'
                ),
//                array(
//                    'label' => 'Grupos do usuário',
//                    'role' => 'ROLE_ADMIN_USERS_GROUPS'
//                )
//                array(
//                    'label' => 'Personificar usuários',
//                    'role' => 'ROLE_ALLOWED_TO_SWITCH'
//                ),
//                  array(
//                    'label' => 'Permissões de usuários',
//                    'role' => 'ROLE_ADMIN_USERS_PERMISSIONS'
//                )
                      
                      
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
                    'label' => 'Excluir grupo',
                    'role' => 'ROLE_ADMIN_USERGROUPS_DELETE'
                ),
                array(
                    'label' => 'Permissões de grupos',
                    'role' => 'ROLE_ADMIN_USERGROUPS_PERMISSIONS'
                )
            )
        ));
    }
}