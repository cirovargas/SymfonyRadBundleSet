<?php
namespace Core\Bundle\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Core\Bundle\UserBundle\Repository\UserRepository")
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
     * @ORM\ManyToMany(targetEntity="Core\Bundle\UserBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
        
    /**
     * @ORM\OneToOne(targetEntity="Core\Bundle\UserBundle\Entity\Profile", mappedBy="user",cascade={"ALL"}, fetch="EAGER")
     */
    private $profile;
    
    public function __construct()
    {
        parent::__construct();
        $this->profile = new Profile();
        $this->profile->setUser($this);
        
    }
    
    public function __toString()
    {
        if($this->profile != null && trim($this->profile->getName()) != '')
            return $this->profile->getName().' ('.(string)$this->getUsername().')';
        
        return (string) $this->getUsername();
    }
    
    function getProfile() {
        return $this->profile;
    }

    function setProfile($profile) {
        $profile->setUser($this);
        $this->profile = $profile;
    }
        
    public function getUserRoles(){
        return $this->roles;
    }
    
    public function setUserRoles($userRoles){
        $this->roles = $userRoles;
    }
    
    public function getName(){
        return isset($this->profile) && trim($this->profile->getName()) != '' ? $this->profile->getName() : $this->username;
    }
    

    public function hasRole($role)
    {
        return parent::hasRole($role);
    }
    
}
