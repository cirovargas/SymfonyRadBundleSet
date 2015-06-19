<?php

namespace Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Profile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surnames", type="string", length=255)
     */
    private $surnames;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="cellphone", type="string", length=255)
     */
    private $cellphone;

    /**
     * @var string
     *
     * @ORM\Column(name="workphone", type="string", length=255)
     */
    private $workphone;

    /**
     * @var string
     *
     * @ORM\Column(name="born_date", type="date")
     */
    private $bornDate;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1)
     */
    private $gender;
    
    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text")
     */
    private $about;
    
    /**
     * @var string
     *
     * @ORM\Column(name="configs", type="array")
     */
    private $configs;

    /**
     * @var \Upload
     *
     * @ORM\ManyToOne(targetEntity="Core\UploadBundle\Entity\Upload", cascade={"ALL"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="avatar", referencedColumnName="id")
     * })
     */
    private $avatar;
    
    /**
     * @var \Upload
     *
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="profile")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;
    

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setSurnames($surnames)
    {
        $this->surnames = $surnames;

        return $this;
    }

    public function getSurnames()
    {
        return $this->surnames;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    public function getCellphone()
    {
        return $this->cellphone;
    }

    public function setWorkphone($workphone)
    {
        $this->workphone = $workphone;

        return $this;
    }

    public function getWorkphone()
    {
        return $this->workphone;
    }

    public function setBornDate($bornDate)
    {
        $this->bornDate = $bornDate;

        return $this;
    }

    public function getBornDate()
    {
        return $this->bornDate;
    }

    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGender()
    {
        return $this->gender;
    }
    
    function getAbout(){
        return $this->about;
    }

    function setAbout($about){
        $this->about = $about;
    }
    
    function getConfigs(){
        return $this->configs;
    }

    function setConfigs($configs){
        $this->configs = $configs;
        return $this;
    }

    public function setAvatar($avatar){
        $this->avatar = $avatar;
        return $this;
    }

    public function getAvatar(){
        return $this->avatar;
    }
    
    function getUser(){
        return $this->user;
    }

    function setUser($user){
        $this->user = $user;
        return $this;
    }
}
