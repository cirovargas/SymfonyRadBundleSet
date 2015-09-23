<?php

namespace Core\AlertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alert
 *
 * @ORM\Table(name="alert")
 * @ORM\Entity
 */
class Alert
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
     * @ORM\Column(name="system", type="string", length=255)
     */
    private $system;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255)
     */
    private $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=255)
     */
    private $level;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Core\AlertBundle\Entity\UserAlert",mappedBy="alert")
     */
    private $users;

    public function __construct(){
        $this->date = new \DateTime();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(){
        return $this->id;
    }

    public function setSystem($system){
        $this->system = $system;

        return $this;
    }

    public function getSystem(){
        return $this->system;
    }

    function getText() {
        return $this->text;
    }

    function setText($text) {
        $this->text = $text;
        return $this;
    }

    
    public function setUrl($url){
        $this->url = $url;

        return $this;
    }

    public function getUrl(){
        return $this->url;
    }

    public function setIcon($icon){
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(){
        return $this->icon;
    }

    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    public function getLevel(){
        return $this->level;
    }
    
    public function getDate() {
        return $this->date;
    }

    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    public function getUsers() {
        return $this->users;
    }

    public function setUsers($users) {
        $this->users = $users;
        return $this;
    }

    public function addUser($user){
        $user->setAlert($this);
        $this->users->add($user);
    }
    
    public function removeUser($user){
        $this->users->removeElement($user);
    }

}
