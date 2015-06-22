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
     * @ORM\ManyToMany(targetEntity="Core\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /**
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\Profile", mappedBy="user",cascade={"ALL"})
     */
    private $profile;

    public function __construct()
    {
        parent::__construct();
        $this->profile = new Profile;
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
}