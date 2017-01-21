<?php

namespace Core\Bundle\AlertaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserAlert
 *
 * @ORM\Table(name="alerta_user")
 * @ORM\Entity
 */
class UserAlerta
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
     *
     * @ORM\ManyToOne(targetEntity="Core\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Alerta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="alert_id", referencedColumnName="id")
     * })
     */
    private $alert;

    /**
     * @var boolean
     *
     * @ORM\Column(name="readed", type="boolean",nullable=true)
     */
    private $readed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_readed", type="datetime",nullable=true)
     */
    private $dateReaded;

    public function getId()
    {
        return $this->id;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setReaded($readed)
    {
        $this->readed = $readed;

        return $this;
    }

    public function getReaded()
    {
        return $this->readed;
    }

    public function setDateReaded($dateReaded)
    {
        $this->dateReaded = $dateReaded;

        return $this;
    }

    public function getDateReaded()
    {
        return $this->dateReaded;
    }
    
    function getAlert() {
        return $this->alert;
    }

    function setAlert($alert) {
        $this->alert = $alert;
        return $this;
    }
}