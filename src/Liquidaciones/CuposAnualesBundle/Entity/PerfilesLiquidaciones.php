<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PerfilesLiquidaciones
 *
 * @ORM\Table("LIQUIDACIONES.PerfilesLiquidaciones")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\PerfilesLiquidacionesRepository")
 */
class PerfilesLiquidaciones
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
     * @ORM\Column(name="Perfil", type="string", length=60)
     */
    private $perfil;


    /**
     * @var string
     *
     * @ORM\Column(name="Activo", type="string", length=1)
     */
    private $activo;


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
     * @ORM\ManyToOne(targetEntity="PerfilUsuarios", inversedBy="PerfilesLiquidaciones")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefPerfil")
     */
    
    
    /**
     * Set perfil
     *
     * @param string $perfil
     * @return PerfilesLiquidaciones
     */
    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;

        return $this;
    }

    /**
     * Get perfil
     *
     * @return string 
     */
    public function getPerfil()
    {
        return $this->perfil;
    }


    /**
     * Set activo
     *
     * @param string $activo
     * @return PerfilesLiquidaciones
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return string 
     */
    public function getActivo()
    {
        return $this->activo;
    }
}
