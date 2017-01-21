<?php

namespace Core\Bundle\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 *
 * @ORM\Table(name="user_profile")
 * @ORM\Entity
 */
class Profile //implements \ArrayAccess
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
     * @ORM\Column(name="name", type="string", length=255,nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="nome_completo", type="string", length=255,nullable=true)
     */
    private $nomeCompleto;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255,nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="cellphone", type="string", length=255,nullable=true)
     */
    private $cellphone;

    /**
     * @var string
     *
     * @ORM\Column(name="workphone", type="string", length=255,nullable=true)
     */
    private $workphone;

    /**
     * @var string
     *
     * @ORM\Column(name="born_date", type="date",nullable=true)
     */
    private $bornDate;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1,nullable=true)
     */
    private $gender;
    
    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text",nullable=true)
     */
    private $about;
    
    /**
     * @var string
     *
     * @ORM\Column(name="atividadesDesenvolvidas", type="text",nullable=true)
     */
    private $atividadesDesenvolvidas;
    
    /**
     * @var string
     *
     * @ORM\Column(name="aptidao", type="text",nullable=true)
     */
    private $aptidao;
    
    /**
     * @var string
     *
     * @ORM\Column(name="configs", type="array")
     */
    private $configs;

    /**
     * @var \Upload
     *
     * @ORM\ManyToOne(targetEntity="Core\Bundle\UploadBundle\Entity\Upload", cascade={"PERSIST"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="avatar", referencedColumnName="id")
     * })
     */
    private $avatar;
    
    /**
     *
     * @ORM\OneToOne(targetEntity="Core\Bundle\UserBundle\Entity\User", inversedBy="profile",cascade={"ALL"})
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
        return !isset($this->name) || trim($this->name) == ''? $this->user->getUsername():$this->name;
    }

    /**
     * @return string
     */
    public function getNomeCompleto()
    {
        return $this->nomeCompleto;
    }

    /**
     * @param string $nomeCompleto
     */
    public function setNomeCompleto($nomeCompleto)
    {
        $this->nomeCompleto = $nomeCompleto;
        return $this;
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
    
    public function getAtividadesDesenvolvidas() {
        return $this->atividadesDesenvolvidas;
    }

    public function getAptidao() {
        return $this->aptidao;
    }

    public function setAtividadesDesenvolvidas($atividadesDesenvolvidas) {
        $this->atividadesDesenvolvidas = $atividadesDesenvolvidas;
        return $this;
    }

    public function setAptidao($aptidao) {
        $this->aptidao = $aptidao;
        return $this;
    }


}
