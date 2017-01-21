<?php

namespace Core\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_group")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="name",
 *          column=@ORM\Column(
 *              name     = "name",
 *              type     = "string",
 *              length   = 255,
 *              nullable = false,
 *              unique   = false
 *          )
 *      )
 * })
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
     protected $id;

    
     /**
      * @ORM\ManyToMany(targetEntity="Core\Bundle\UserBundle\Entity\User", mappedBy="groups")
      **/
     private $users;
    
    
    
    public function __construct(){
        parent::__construct('');
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }
     
    public function __toString() {
        return $this->name;
    }
     
    function getUsers() {
        return $this->users;
    }

    function setUsers($users) {
        $this->users = $users;
        return $this;
    }

    function addUser($user){
        $user->addGroup($this);
        $this->users->add($user);
    }
    
    function removeUser($user){
        $user->removeGroup($this);
        $this->users->removeElement($user); 
    }
    
    function getInstancia() {
        return $this->instancia;
    }

    function setInstancia($instancia) {
        $this->instancia = $instancia;
        return $this;
    }
}
