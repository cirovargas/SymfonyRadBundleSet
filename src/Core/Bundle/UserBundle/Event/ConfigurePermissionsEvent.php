<?php

namespace Core\Bundle\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ConfigurePermissionsEvent extends Event
{
    
    private $permissions;

    public function __construct($permissions)
    {
        $this->permissions = $permissions;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }
    
    public function addRootNode($node,$tree){
        $this->permissions[$node] = $tree;
    }
}