<?php

namespace Core\Bundle\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/*
 * @ORM\Entity
 * @ORM\Table(name="upload")
 */
class UploadImport
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
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    function getName() {
        return $this->name;
    }

    function getExtension() {
        return $this->extension;
    }

    function getPath() {
        return $this->path;
    }

    function setName($name) {
        $this->name = $name;
        return $this;
    }

    function setExtension($extension) {
        $this->extension = $extension;
        return $this;
    }

    function setPath($path) {
        $this->path = $path;
        return $this;
    }


}
