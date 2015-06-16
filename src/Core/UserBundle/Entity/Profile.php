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
     * @var \Upload
     *
     * @ORM\ManyToOne(targetEntity="Core\UploadBundle\Entity\Upload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="avatar", referencedColumnName="id")
     * })
     */
    private $avatar;
    
    /**
     * @var \Upload
     *
     * @ORM\OneToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;
    

    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Profile
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surnames
     *
     * @param string $surnames
     * @return Profile
     */
    public function setSurnames($surnames)
    {
        $this->surnames = $surnames;

        return $this;
    }

    /**
     * Get surnames
     *
     * @return string 
     */
    public function getSurnames()
    {
        return $this->surnames;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Profile
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set cellphone
     *
     * @param string $cellphone
     * @return Profile
     */
    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * Get cellphone
     *
     * @return string 
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * Set workphone
     *
     * @param string $workphone
     * @return Profile
     */
    public function setWorkphone($workphone)
    {
        $this->workphone = $workphone;

        return $this;
    }

    /**
     * Get workphone
     *
     * @return string 
     */
    public function getWorkphone()
    {
        return $this->workphone;
    }

    /**
     * Set bornDate
     *
     * @param string $bornDate
     * @return Profile
     */
    public function setBornDate($bornDate)
    {
        $this->bornDate = $bornDate;

        return $this;
    }

    /**
     * Get bornDate
     *
     * @return string 
     */
    public function getBornDate()
    {
        return $this->bornDate;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return Profile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }
    
    function getAbout() {
        return $this->about;
    }

    function setAbout($about) {
        $this->about = $about;
    }

    
    /**
     * Set avatar
     *
     * @param integer $avatar
     * @return Profile
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return integer 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
    
    function getUser() {
        return $this->user;
    }

    function setUser( $user) {
        $this->user = $user;
    }
}
