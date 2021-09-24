<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Liquidaciones
 *
 * @ORM\Table("LiquidacionesWeb.dbo.liquidaciones")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\LiquidacionesRepository")
 */
class Liquidaciones
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
     * @ORM\Column(name="RefCupoTipoLiquidacion", type="integer")
     */
    private $refCupoTipoLiquidacion;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdPersonalCargo", type="integer")
     */
    private $idPersonalCargo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdConcepto", type="integer")
     */
    private $idConcepto;

    /**
     * @var decimal
     *
     * @ORM\Column(name="HsExValorHora", type="decimal",precision=10, scale=2, nullable=true)
     * @GRID\Column(title="HsExValorHora", visible=true, filterable=false, type="number", align="right", style="decimal", precision=2)
     */
    private $hsExValorHora;

    /**
     * @var integer
     *
     * @ORM\Column(name="HsExCantSimples", type="smallint", nullable=true)
     */
    private $hsExCantSimples;

    /**
     * @var integer
     *
     * @ORM\Column(name="HsExCantDobles", type="smallint", nullable=true)
     */
    private $hsExCantDobles;

    /**
     * @var integer
     *
     * @ORM\Column(name="RGIdPersonalCargo", type="integer", nullable=true)
     */
    private $rGIdPersonalCargo;

    /**
     * @var \Date
     *
     * @ORM\Column(name="RGFecha", type="date", nullable=true)
     */
    private $rGFecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="RGCantHsGuardia", type="smallint", nullable=true)
     */
    private $rGCantHsGuardia;

    /**
     * @var integer
     *
     * @ORM\Column(name="RGIdNovedad", type="integer", nullable=true)
     */
    private $rGIdNovedad;

    /**
     * @var decimal
     *
     * @ORM\Column(name="MontoTotalCalculado", type="decimal",precision=10, scale=2, nullable=true)
     */
    private $montoTotalCalculado;

    /**
     * @var string
     *
     * @ORM\Column(name="RequiereAutorizacion", type="string", length=1, nullable=true)
     */
    private $requiereAutorizacion = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="UsuaAutoriza", type="string", length=20, nullable=true)
     */
    private $usuaAutoriza;

    /**
     * @var string
     *
     * @ORM\Column(name="UsuaCrea", type="string", length=20, nullable=true)
     */
    private $usuaCrea;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaCrea", type="datetime", nullable=true)
     */
    private $fechaCrea;


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
     * Set refCupoTipoLiquidacion
     *
     * @param integer $refCupoTipoLiquidacion
     * @return Liquidaciones
     */
    public function setRefCupoTipoLiquidacion($refCupoTipoLiquidacion)
    {
        $this->refCupoTipoLiquidacion = $refCupoTipoLiquidacion;

        return $this;
    }

    /**
     * Get refCupoTipoLiquidacion
     *
     * @return integer 
     */
    public function getRefCupoTipoLiquidacion()
    {
        return $this->refCupoTipoLiquidacion;
    }

    /**
     * Set idPersonalCargo
     *
     * @param integer $idPersonalCargo
     * @return Liquidaciones
     */
    public function setIdPersonalCargo($idPersonalCargo)
    {
        $this->idPersonalCargo = $idPersonalCargo;

        return $this;
    }

    /**
     * Get idPersonalCargo
     *
     * @return integer 
     */
    public function getIdPersonalCargo()
    {
        return $this->idPersonalCargo;
    }

    /**
     * Set idConcepto
     *
     * @param integer $idConcepto
     * @return Liquidaciones
     */
    public function setIdConcepto($idConcepto)
    {
        $this->idConcepto = $idConcepto;

        return $this;
    }

    /**
     * Get idConcepto
     *
     * @return integer 
     */
    public function getIdConcepto()
    {
        return $this->idConcepto;
    }

    /**
     * Set hsExValorHora
     *
     * @param string $hsExValorHora
     * @return Liquidaciones
     */
    public function setHsExValorHora($hsExValorHora)
    {
        $this->hsExValorHora = $hsExValorHora;

        return $this;
    }

    /**
     * Get hsExValorHora
     *
     * @return string 
     */
    public function getHsExValorHora()
    {
        return $this->hsExValorHora;
    }

    /**
     * Set hsExCantSimples
     *
     * @param integer $hsExCantSimples
     * @return Liquidaciones
     */
    public function setHsExCantSimples($hsExCantSimples)
    {
        $this->hsExCantSimples = $hsExCantSimples;

        return $this;
    }

    /**
     * Get hsExCantSimples
     *
     * @return integer 
     */
    public function getHsExCantSimples()
    {
        return $this->hsExCantSimples;
    }

    /**
     * Set hsExCantDobles
     *
     * @param integer $hsExCantDobles
     * @return Liquidaciones
     */
    public function setHsExCantDobles($hsExCantDobles)
    {
        $this->hsExCantDobles = $hsExCantDobles;

        return $this;
    }

    /**
     * Get hsExCantDobles
     *
     * @return integer 
     */
    public function getHsExCantDobles()
    {
        return $this->hsExCantDobles;
    }

    /**
     * Set rGIdPersonalCargo
     *
     * @param integer $rGIdPersonalCargo
     * @return Liquidaciones
     */
    public function setRGIdPersonalCargo($rGIdPersonalCargo)
    {
        $this->rGIdPersonalCargo = $rGIdPersonalCargo;

        return $this;
    }

    /**
     * Get rGIdPersonalCargo
     *
     * @return integer 
     */
    public function getRGIdPersonalCargo()
    {
        return $this->rGIdPersonalCargo;
    }

    /**
     * Set rGFecha
     *
     * @param \Date $rGFecha
     * @return Liquidaciones
     */
    public function setRGFecha($rGFecha)
    {
        $this->rGFecha = $rGFecha;

        return $this;
    }

    /**
     * Get rGFecha
     *
     * @return \Date 
     */
    public function getRGFecha()
    {
        return $this->rGFecha;
    }

    /**
     * Set rGCantHsGuardia
     *
     * @param integer $rGCantHsGuardia
     * @return Liquidaciones
     */
    public function setRGCantHsGuardia($rGCantHsGuardia)
    {
        $this->rGCantHsGuardia = $rGCantHsGuardia;

        return $this;
    }

    /**
     * Get rGCantHsGuardia
     *
     * @return integer 
     */
    public function getRGCantHsGuardia()
    {
        return $this->rGCantHsGuardia;
    }

    /**
     * Set rGIdNovedad
     *
     * @param integer $rGIdNovedad
     * @return Liquidaciones
     */
    public function setRGIdNovedad($rGIdNovedad)
    {
        $this->rGIdNovedad = $rGIdNovedad;

        return $this;
    }

    /**
     * Get rGIdNovedad
     *
     * @return integer 
     */
    public function getRGIdNovedad()
    {
        return $this->rGIdNovedad;
    }

    /**
     * Set montoTotalCalculado
     *
     * @param decimal $montoTotalCalculado
     * @return Liquidaciones
     */
    public function setMontoTotalCalculado($montoTotalCalculado)
    {
        $this->montoTotalCalculado = $montoTotalCalculado;

        return $this;
    }

    /**
     * Get montoTotalCalculado
     *
     * @return decimal 
     */
    public function getMontoTotalCalculado()
    {
        return $this->montoTotalCalculado;
    }

    /**
     * Set requiereAutorizacion
     *
     * @param string $requiereAutorizacion
     * @return Liquidaciones
     */
    public function setRequiereAutorizacion($requiereAutorizacion)
    {
        $this->requiereAutorizacion = $requiereAutorizacion;

        return $this;
    }

    /**
     * Get requiereAutorizacion
     *
     * @return string 
     */
    public function getRequiereAutorizacion()
    {
        return $this->requiereAutorizacion;
    }

    /**
     * Set usuaAutoriza
     *
     * @param string $usuaAutoriza
     * @return Liquidaciones
     */
    public function setUsuaAutoriza($usuaAutoriza)
    {
        $this->usuaAutoriza = $usuaAutoriza;

        return $this;
    }

    /**
     * Get usuaAutoriza
     *
     * @return string 
     */
    public function getUsuaAutoriza()
    {
        return $this->usuaAutoriza;
    }

    /**
     * Set usuaCrea
     *
     * @param string $usuaCrea
     * @return Liquidaciones
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
     * Set fechaCrea
     *
     * @param \DateTime $fechaCrea
     * @return Liquidaciones
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
     * @ORM\ManyToOne(targetEntity="CuposHATiposLiquidacion")
     * @ORM\JoinColumn(name="RefCupoTipoLiquidacion", referencedColumnName="id", nullable=false)
     */
    protected $cuposhatipoliquidacion;
    
    public function __construct() {
       
        $this->cuposhatipoliquidacion = new ArrayCollection();
        $this->fechaCrea = new \DateTime();
    }
    

    /**
     * Set cuposhatipoliquidacion
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion $cuposhatipoliquidacion
     * @return Liquidaciones
     */
    public function setCuposhatipoliquidacion(\Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion $cuposhatipoliquidacion)
    {
        $this->cuposhatipoliquidacion = $cuposhatipoliquidacion;

        return $this;
    }

    /**
     * Get cuposhatipoliquidacion
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion 
     */
    public function getCuposhatipoliquidacion()
    {
        return $this->cuposhatipoliquidacion;
    }
}
