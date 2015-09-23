<?php

namespace Core\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_group")
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
     * @var string
     *
     * @ORM\Column(name="is_unidade_administrativa", type="boolean",nullable=true)
     */
    private $isUnidadeAdministrativa;
    
    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="chefe_id", referencedColumnName="id")
     * })
     */
    private $chefe;
     
     
    /**
     * @var string
     *
     * @ORM\Column(name="ldap_cn", type="string", length=255,nullable=true)
     */
    private $ldapCn;
    
    /**
     * @ORM\ManyToMany(targetEntity="Core\UserBundle\Entity\User", mappedBy="groups")
     **/
    private $users;
    
    public function __construct()
    {
        parent::__construct('');
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        
    }
     
     
    public function __toString() {
        return $this->name;
    }
     
    function getLdapCn() {
        return $this->ldapCn;
    }

    function setLdapCn($ldapCn) {
        $this->ldapCn = $ldapCn;
        return $this;
    }
    
    
    function getUsers() {
        return $this->users;
    }

    function setUsers($users) {
        $this->users = $users;
        return $this;
    }

    function addUser($user){
        $this->users[] = $user;
    }
    
    function removeUser($user){
        $this->users->removeElement($user); 
    }
    
    public function getIsUnidadeAdministrativa() {
        return $this->isUnidadeAdministrativa;
    }

    public function getChefe() {
        return $this->chefe;
    }

    public function setIsUnidadeAdministrativa($isUnidadeAdministrativa) {
        $this->isUnidadeAdministrativa = $isUnidadeAdministrativa;
        return $this;
    }

    public function setChefe($chefe) {
        $this->chefe = $chefe;
        return $this;
    }




}