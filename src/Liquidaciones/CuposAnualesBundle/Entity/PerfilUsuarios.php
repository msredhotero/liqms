<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Liquidaciones\ReferenciasBundle\Entity\Dependencias;

/**
 * PerfilUsuarios
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\PerfilUsuariosRepository")
 */
class PerfilUsuarios
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
     * @var integer
     *
     * @ORM\Column(name="RefUsuario", type="integer")
     */
    private $refUsuario;

    /**
     * @var integer
     *
     * @ORM\Column(name="RefPerfil", type="integer")
     */
    private $refPerfil;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="RefDependencia", type="integer")
     */
    private $refDependencia;
    

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
     * Set refUsuario
     *
     * @param integer $refUsuario
     * @return PerfilUsuarios
     */
    public function setRefUsuario($refUsuario)
    {
        $this->refUsuario = $refUsuario;

        return $this;
    }

    /**
     * Get refUsuario
     *
     * @return integer 
     */
    public function getRefUsuario()
    {
        return $this->refUsuario;
    }

    /**
     * Set refPerfil
     *
     * @param integer $refPerfil
     * @return PerfilUsuarios
     */
    public function setRefPerfil($refPerfil)
    {
        $this->refPerfil = $refPerfil;

        return $this;
    }

    /**
     * Get refPerfil
     *
     * @return integer 
     */
    public function getRefPerfil()
    {
        return $this->refPerfil;
    }
    
    
    /**
     * @ORM\ManyToOne(targetEntity="Liquidaciones\ReferenciasBundle\Entity\Dependencias")
     * @ORM\JoinColumn(name="refDependencia", referencedColumnName="id", nullable=false)
     */
    protected $dependencias;
    
    /**
     * @ORM\ManyToOne(targetEntity="PerfilesLiquidaciones")
     * @ORM\JoinColumn(name="RefPerfil", referencedColumnName="id", nullable=false)
     */
    protected $perfilesliquidaciones;
    
    public function __construct() {

        $this->perfilesliquidaciones = new ArrayCollection();
        $this->dependencias = new ArrayCollection();
    }
    
    
    /**
     * Set dependencias
     *
     * @param \Liquidaciones\ReferenciasBundle\Entity\Dependencias $dependencias
     * @return Dependencias
     */
    public function setDependencias(\Liquidaciones\ReferenciasBundle\Entity\Dependencias $dependencias)
    {
        $this->dependencias = $dependencias;

        return $this;
    }

    /**
     * Get dependencias
     *
     * @return \Liquidaciones\ReferenciasBundle\Entity\Dependencias 
     */
    public function getDependencias()
    {
        return $this->dependencias;
    }
    
    
    /**
     * Set perfilesliquidaciones
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\PerfilesLiquidaciones $perfilesliquidaciones
     * @return Cupos
     */
    public function setPerfilesLiquidaciones(\Liquidaciones\CuposAnualesBundle\Entity\PerfilesLiquidaciones $perfilesliquidaciones=null)
    {
        $this->perfilesliquidaciones = $perfilesliquidaciones;

        return $this;
    }

    /**
     * Get perfilesliquidaciones
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\PerfilesLiquidaciones 
     */
    public function getPerfilesLiquidaciones()
    {
        return $this->perfilesliquidaciones;
    }
}
