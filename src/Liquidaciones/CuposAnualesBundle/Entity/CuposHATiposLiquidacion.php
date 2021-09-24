<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * CuposHATiposLiquidacion
 *
 * @ORM\Table("LiquidacionesWeb.dbo.cuposhatiposliquidacion")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacionRepository")
 * @GRID\Source(columns="id, refCupo, cupos.anio, cupos.mes, cupos.monto, cupos.cupoestados.cupoEstado, dependen,cupos.dependencias.codigo ,cuentas.cuenta, adicional, cupos.cuposanuales.descripcion", groups={"admin"})
 * @GRID\Source(columns="id, refCupo, cupos.anio, cupos.mes, cupos.monto, cupos.cupoestados.cupoEstado, dependencia,cupos.dependencias.codigo ,cuentas.cuenta, adicional, cupos.cuposanuales.descripcion", groups={"usuario"})
 * @GRID\Column(id="dependencia", type="join", title="Dependencia", columns={"cupos.dependencias.codigo"}, separator="", visible=false, groups={"usuario"})
 * @GRID\Column(id="dependen", type="join", title="Dependencia", columns={"cupos.dependencias.codigo"}, separator="", visible=true, groups={"admin"})
 */
class CuposHATiposLiquidacion
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
     * @ORM\Column(name="RefCuenta", type="integer")
     */
    private $refCuenta;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdImputacionPresupuestaria", type="integer")
     */
    private $idImputacionPresupuestaria;

    /**
     * @var integer
     *
     * @ORM\Column(name="Adicional", type="smallint")
     * @GRID\Column(title="Adic", visible=true, operators={"eq"},defaultOperator="eq", operatorsVisible=false, filterable=false)
     */
    private $adicional;

    /**
     * @var integer
     *
     * @ORM\Column(name="RefCupo", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $refCupo;


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
     * @ORM\ManyToOne(targetEntity="Liquidaciones", inversedBy="CuposHATiposLiquidacion")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCupoTipoLiquidacion")
     */
    
    
    
    /**
     * Set refCuenta
     *
     * @param integer $refCuenta
     * @return CuposHATiposLiquidacion
     */
    public function setRefCuenta($refCuenta)
    {
        $this->refCuenta = $refCuenta;

        return $this;
    }

    /**
     * Get refCuenta
     *
     * @return integer 
     */
    public function getRefCuenta()
    {
        return $this->refCuenta;
    }

    /**
     * Set idImputacionPresupuestaria
     *
     * @param integer $idImputacionPresupuestaria
     * @return CuposHATiposLiquidacion
     */
    public function setIdImputacionPresupuestaria($idImputacionPresupuestaria)
    {
        $this->idImputacionPresupuestaria = $idImputacionPresupuestaria;

        return $this;
    }

    /**
     * Get idImputacionPresupuestaria
     *
     * @return integer 
     */
    public function getIdImputacionPresupuestaria()
    {
        return $this->idImputacionPresupuestaria;
    }

    /**
     * Set adicional
     *
     * @param integer $adicional
     * @return CuposHATiposLiquidacion
     */
    public function setAdicional($adicional)
    {
        $this->adicional = $adicional;

        return $this;
    }

    /**
     * Get adicional
     *
     * @return integer 
     */
    public function getAdicional()
    {
        return $this->adicional;
    }

    /**
     * Set refCupo
     *
     * @param integer $refCupo
     * @return CuposHATiposLiquidacion
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
     * @ORM\ManyToOne(targetEntity="Cuentas")
     * @ORM\JoinColumn(name="RefCuenta", referencedColumnName="id", nullable=false)
     * @GRID\Column(field="cuentas.cuenta", type="text", title="Cuenta", operatorsVisible=false, filter="select")
     */
    protected $cuentas;
    
    /**
     * @ORM\ManyToOne(targetEntity="Cupos")
     * @ORM\JoinColumn(name="RefCupo", referencedColumnName="id", nullable=false)
     * @GRID\Column(field="cupos.id", visible=false, filterable=false)
     * @GRID\Column(field="cupos.anio", type="text", title="Año", filterable=false)
     * @GRID\Column(field="cupos.mes", type="text", title="Mes", filterable=false)
     * @GRID\Column(field="cupos.monto", type="number", title="Monto", defaultOperator="eq", operatorsVisible=false, filterable=false, operators={"eq"}, style="decimal", precision=2)
     * @GRID\Column(field="cupos.cupoestados.cupoEstado", type="text", title="Estado")
     * @GRID\Column(field="cupos.cuposanuales.descripcion", title="Cupo Anual", filterable=true)
     * @GRID\Column(field="cupos.dependencias.nombre", type="text", title="Depe", operatorsVisible=false, visible=false, filterable=false)
     * @GRID\Column(field="cupos.dependencias.codigo", type="text", title="Codigo", operatorsVisible=false, visible=false, filterable=false)
     * @GRID\Column(field="cupos.dependencias.tipodependencia.codTipoDependencia", type="text", title="TipoDepe", operatorsVisible=true, visible=true, filterable=true)
     */
    protected $cupos;
    
    public function __construct() {
       
        $this->cuentas = new ArrayCollection();
        $this->cupos = new ArrayCollection();
    }

  

    /**
     * Set cuentas
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\Cuentas $cuentas
     * @return CuposHATiposLiquidacion
     */
    public function setCuentas(\Liquidaciones\CuposAnualesBundle\Entity\Cuentas $cuentas=null)
    {
        $this->cuentas = $cuentas;

        return $this;
    }

    /**
     * Get cuentas
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\Cuentas 
     */
    public function getCuentas()
    {
        return $this->cuentas;
    }
    
    
    /**
     * Set cupos
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\Cupos $cupos
     * @return CuposHATiposLiquidacion
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
        return $this->getCupos()->getCuposanuales()->getDescripcion();
    }
}
