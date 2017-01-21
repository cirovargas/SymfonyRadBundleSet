<?php

namespace Core\Bundle\UserBundle\Services;

use Core\Bundle\UserBundle\Event\ConfigurePermissionsEvent;
use Core\Bundle\UserBundle\CoreUserEvents;

class SystemRoles {
    
    private $dispatcher;
    
    public function __construct($dispatcher) {
        $this->dispatcher = $dispatcher;
    }
    
    public function getRoles(){
        
        
        $roles = array();
        
        $event = new ConfigurePermissionsEvent($roles);
        $this->dispatcher->dispatch(CoreUserEvents::CREATE_PERMISSIONS_TREE,$event);
        
        return $event->getPermissions();
    }
    
    
    public function getRolesArray(){
        $roles = array();
        
        $event = new ConfigurePermissionsEvent($roles);
        $this->dispatcher->dispatch(CoreUserEvents::CREATE_PERMISSIONS_TREE,$event);
        
        return $this->getRole($event->getPermissions());
    }
    
    private function getRole($permissions){
        $roles = array();
        foreach($permissions as $permission){
            $roles[$permission['role']] = $permission['label'];
            if(isset($permission['children']) && count($permission['children']) >0 ){
                $roles = array_merge($roles,$this->getRole($permission['children']));
            }
        }
        return $roles;
    }
}