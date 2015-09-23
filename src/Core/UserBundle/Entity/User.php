<?php
namespace Core\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="is_ldap",type="boolean")
     */
    protected $ldap;
    
    /**
     * @ORM\ManyToMany(targetEntity="Core\UserBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\Profile", mappedBy="user",cascade={"ALL"}, fetch="EAGER")
     */
    private $profile;

    public function __construct()
    {
        parent::__construct();
        $this->ldap = true;
        $this->profile = new Profile();
        $this->profile->setUser($this);
    }
    
    function getProfile() {
        return $this->profile;
    }

    function setProfile($profile) {
        $profile->setUser($this);
        $this->profile = $profile;
    }
    
   function setProfileNameLdap($name){
       
       $name = explode(' ',$name);
       
       $this->profile->setName($name[0]);
       unset($name[0]);
       $name = implode(' ',$name);
       $this->profile->setSurnames($name);
       
   }
   
   function setEmailLdap($email){
       
       
       $this->setEmail($email);
   }

        
    public function getUserRoles(){
        return $this->roles;
    }
    
    public function setUserRoles($userRoles){
        $this->roles = $userRoles;
    }
    
    function getLdap() {
        return $this->ldap;
    }

    function setLdap($ldap) {
        $this->ldap = $ldap;
        return $this;
    }
    
    public function getName(){
        return isset($this->profile) && trim($this->profile->getName()) != '' ? $this->profile->getName() : $this->username;
    }


}