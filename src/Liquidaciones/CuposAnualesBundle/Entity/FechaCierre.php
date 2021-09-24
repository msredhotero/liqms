<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FechaCierre
 *
 * @ORM\Table("LiquidacionesWeb.dbo.fechacierre")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\FechaCierreRepository")
 */
class FechaCierre
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
     * @ORM\Column(name="RefCupo", type="integer")
     */
    private $refCupo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaDesde", type="datetime")
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaHasta", type="datetime")
     */
    private $fechaHasta;

    /**
     * @var string
     *
     * @ORM\Column(name="UsuaCrea", type="string", length=120)
     */
    private $usuaCrea;

    /**
     * @var string
     *
     * @ORM\Column(name="UsuaModi", type="string", length=120)
     */
    private $usuaModi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaCrea", type="datetime")
     */
    private $fechaCrea;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaModi", type="datetime")
     */
    private $fechaModi;

    protected $cupos;
    
    public function __construct() {
       
        $this->cupos = new ArrayCollection();
    }
    
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
     * Set refCupo
     *
     * @param integer $refCupo
     * @return FechaCierre
     */
    public function setRefCupo($refCupo)
    {
        $this->refCupo = $refCupo;

        return $this;
    }

    /**
     * Get refCupo
     *
     * @return integer 
     */
    public function getRefCupo()
    {
        return $this->refCupo;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return FechaCierre
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;

        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return FechaCierre
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;

        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime 
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Set usuaCrea
     *
     * @param string $usuaCrea
     * @return FechaCierre
     */
    public function setUsuaCrea($usuaCrea)
    {
        $this->usuaCrea = utf8_decode($usuaCrea);

        return $this;
    }

    /**
     * Get usuaCrea
     *
     * @return string 
     */
    public function getUsuaCrea()
    {
        return utf8_encode($this->usuaCrea);
    }

    /**
     * Set usuaModi
     *
     * @param string $usuaModi
     * @return FechaCierre
     */
    public function setUsuaModi($usuaModi)
    {
        $this->usuaModi = utf8_decode($usuaModi);

        return $this;
    }

    /**
     * Get usuaModi
     *
     * @return string 
     */
    public function getUsuaModi()
    {
        return utf8_encode($this->usuaModi);
    }

    /**
     * Set fechaCrea
     *
     * @param \DateTime $fechaCrea
     * @return FechaCierre
     */
    public function setFechaCrea($fechaCrea)
    {
        $this->fechaCrea = $fechaCrea;

        return $this;
    }

    /**
     * Get fechaCrea
     *
     * @return \DateTime 
     */
    public function getFechaCrea()
    {
        return $this->fechaCrea;
    }

    /**
     * Set fechaModi
     *
     * @param \DateTime $fechaModi
     * @return FechaCierre
     */
    public function setFechaModi($fechaModi)
    {
        $this->fechaModi = $fechaModi;

        return $this;
    }

    /**
     * Get fechaModi
     *
     * @return \DateTime 
     */
    public function getFechaModi()
    {
        return $this->fechaModi;
    }
    
    /**
     * Set cupos
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\Cupos $cupos
     * @return FechaCierre
     */
    public function setCupos(\Liquidaciones\CuposAnualesBundle\Entity\Cupos $cupos=null)
    {
        $this->cupos = $cupos;

        return $this;
    }

    /**
     * Get cupos
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\Cupos 
     */
    public function getCupos()
    {
        return $this->cupos;
    }
    
    
    public function __toString()
    {
        return utf8_encode($this->getCupos()->getCuposanuales()->getDescripcion());
    }
}
