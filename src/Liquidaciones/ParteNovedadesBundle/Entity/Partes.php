<?php

namespace MinSaludBA\ParteNovedadesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parte
 *
 * @ORM\Table(name="novedades.dbo.Partes")
 * @ORM\Entity
 */
class Partes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IdParte", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $IdParte;

    /**
     * @var string
     *
     * @ORM\Column(name="ParteDescripcion", type="string", length=255)
     */
    private $ParteDescripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="PerfilesAdmitidos", type="string", length=40)
     */
    private $PerfilesAdmitidos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Activo", type="boolean")
     */
    private $Activo;


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
     * Set ParteDescripcion
     *
     * @param string $parteDescripcion
     * @return Partes
     */
    public function setParteDescripcion($parteDescripcion)
    {
        $this->ParteDescripcion = $parteDescripcion;
    
        return $this;
    }

    /**
     * Get ParteDescripcion
     *
     * @return string 
     */
    public function getParteDescripcion()
    {
        return $this->ParteDescripcion;
    }

    /**
     * Set PerfilesAdmitidos
     *
     * @param string $perfilesAdmitidos
     * @return Partes
     */
    public function setPerfilesAdmitidos($perfilesAdmitidos)
    {
        $this->PerfilesAdmitidos = $perfilesAdmitidos;
    
        return $this;
    }

    /**
     * Get PerfilesAdmitidos
     *
     * @return string 
     */
    public function getPerfilesAdmitidos()
    {
        return $this->PerfilesAdmitidos;
    }

    /**
     * Set Activo
     *
     * @param boolean $activo
     * @return Partes
     */
    public function setActivo($activo)
    {
        $this->Activo = $activo;
    
        return $this;
    }

    /**
     * Get Activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->Activo;
    }
}
