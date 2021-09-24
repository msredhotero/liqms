<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Liquidaciones\ReferenciasBundle\Entity\Dependencias;
use Liquidaciones\HaberesBundle\Entity\HADependencia;
use Liquidaciones\CuposAnualesBundle\Entity\Cuentas;
use Liquidaciones\ReferenciasBundle\Entity\TipoDependencia;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * ValorModulo
 *
 * @ORM\Table("LiquidacionesWeb.dbo.ValorModulo")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuposRepository")
 */
class ValorModulo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;


    
    /**
     * @var integer
     *
     * @ORM\Column(name="RefTipoModulos", type="integer")
     */
    private $refTipoModulos;
    


    /**
     * @var decimal
     *
     * @ORM\Column(name="Monto", type="decimal",precision=10, scale=2)
     * @GRID\Column(title="Monto", visible=true, filterable=false, type="number", align="right", style="decimal", precision=2)
     */
    private $monto;

   

    /**
     * @var \Date
     *
     * @ORM\Column(name="VigDesde", type="date", nullable=false)
     */
    private $vigDesde;

    /**
     * @var \Date
     *
     * @ORM\Column(name="VigHasta", type="date", nullable=false)
     */
    private $vigHasta;

    


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
     * Set refTipoModulos
     *
     * @param integer $refTipoModulos
     * @return refTipoModulos
     */
    public function setRefTipoModulos($refTipoModulos)
    {
        $this->refTipoModulos = $refTipoModulos;

        return $this;
    }

    /**
     * Get refTipoModulos
     *
     * @return integer 
     */
    public function getRefTipoModulos()
    {
        return $this->refTipoModulos;
    }
    
    
    

    /**
     * Set monto
     *
     * @param string $monto
     * @return Cupos
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }


    

    /**
     * Set vigDesde
     *
     * @param \Date $vigDesde
     * @return vigDesde
     */
    public function setVigDesde($vigDesde)
    {
        $this->vigDesde = $vigDesde;

        return $this;
    }

    /**
     * Get vigDesde
     *
     * @return \Date
     */
    public function getVigDesde()
    {
        return $this->vigDesde;
    }

    /**
     * Set vigHasta
     *
     * @param \Date $vigHasta
     * @return vigHasta
     */
    public function setVigHasta($vigHasta)
    {
        $this->vigHasta = $vigHasta;

        return $this;
    }

    /**
     * Get vigHasta
     *
     * @return \Date
     */
    public function getVigHasta()
    {
        return $this->vigHasta;
    }
    
    
    
    /**
     * @ORM\ManyToOne(targetEntity="TipoModulos")
     * @ORM\JoinColumn(name="RefTipoModulos", referencedColumnName="id", nullable=false)
     */
    protected $tipomodulos;
    
   

    public function __construct() {

        $this->tipomodulos = new ArrayCollection();
    }

    
    
    /**
     * Set tipomodulos
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\TipoModulos $tipomodulos
     * @return CuposHATiposLiquidacion
     */
    public function setTipoModulos(\Liquidaciones\CuposAnualesBundle\Entity\TipoModulos $cuentas=null)
    {
        $this->tipomodulos = $tipomodulos;

        return $this;
    }

    /**
     * Get tipomodulos
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\TipoModulos 
     */
    public function getTipoModulos()
    {
        return $this->tipomodulos;
    }
    


    public function __toString()
    {
        return $this->getTipoModulos()->getModulo().' - Valor: '.$this->getMonto();
    }


}
