<?php

namespace Core\Bundle\AlertaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alerta
 *
 * @ORM\Table(name="alerta")
 * @ORM\Entity
 */
class Alerta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="string", length=500, nullable=false)
     */
    private $texto;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sistema", type="string", length=45, nullable=false)
     */
    private $sistema;
    
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;
    
    /**
     * @var string
     *
     * @ORM\Column(name="icone", type="string", length=255, nullable=true)
     */
    private $icone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=45, nullable=true)
     */
    private $level;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data", type="datetime", nullable=false)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="text", nullable=true)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="meta", type="array", nullable=true)
     */
    private $meta;
    
    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=25, nullable=true)
     */
    private $route;
    
    /**
     * @var string
     *
     * @ORM\Column(name="route_params", type="array", nullable=true)
     */
    private $routeParams;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Core\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="alerta_has_user",
     *   joinColumns={
     *     @ORM\JoinColumn(name="alerta_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   }
     * )
     */
    private $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Core\Bundle\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="alerta_has_user_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="alerta_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_group_id", referencedColumnName="id")
     *   }
     * )
     */
    private $userGroup;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->instancia = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userGroup = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getId() {
        return $this->id;
    }

    public function getTexto() {
        return $this->texto;
    }

    public function getData() {
        return $this->data;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getMeta() {
        return $this->meta;
    }

    public function getRoute() {
        return $this->route;
    }

    public function getRouteParams() {
        return $this->routeParams;
    }

    public function getUser() {
        return $this->user;
    }

    public function getUserGroup() {
        return $this->userGroup;
    }

    public function setTexto($texto) {
        $this->texto = $texto;
        return $this;
    }

    public function setData(\DateTime $data) {
        $this->data = $data;
        return $this;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
        return $this;
    }

    public function setMeta($meta) {
        $this->meta = $meta;
        return $this;
    }

    public function setRoute($route) {
        $this->route = $route;
        return $this;
    }

    public function setRouteParams($routeParams) {
        $this->routeParams = $routeParams;
        return $this;
    }
    public function setUser(\Doctrine\Common\Collections\Collection $user) {
        $this->user = $user;
        return $this;
    }

    public function setUserGroup(\Doctrine\Common\Collections\Collection $userGroup) {
        $this->userGroup = $userGroup;
        return $this;
    }
    public function getSistema() {
        return $this->sistema;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getIcone() {
        return $this->icone;
    }

    public function setSistema($sistema) {
        $this->sistema = $sistema;
        return $this;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function setIcone($icone) {
        $this->icone = $icone;
        return $this;
    }
    
    public function getLevel() {
        return $this->level;
    }

    public function setLevel($level) {
        $this->level = $level;
        return $this;
    }




}
