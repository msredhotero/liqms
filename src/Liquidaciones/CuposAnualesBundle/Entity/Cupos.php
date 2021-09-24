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
 * Cupos
 *
 * @ORM\Table("LiquidacionesWeb.dbo.cupos")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuposRepository")
 * @GRID\Source(columns="id, anio, mes, monto, cupoestados.cupoEstado, cuposanuales.descripcion,dependencia.nombre, CuposHATiposLiquidacion.adicional")
 */
class Cupos
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
     * @ORM\Column(name="RefCupoAnual", type="integer")
     */
    private $refCupoAnual;
    
    /**
     * @var integer $RefCupoEstado
     *
     * @ORM\Column(name="RefCupoEstado", type="smallint")
     */
    private $RefCupoEstado;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="idDependencia", type="integer")
     */
    private $idDependencia;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="Mes", type="smallint")
     * @GRID\Column(title="Mes", visible=true, filterable=false)
     */
    private $mes;

    /**
     * @var integer
     *
     * @ORM\Column(name="Anio", type="smallint")
     * @GRID\Column(title="Año", visible=true, filterable=false)
     */
    private $anio;

    /**
     * @var decimal
     *
     * @ORM\Column(name="Monto", type="decimal",precision=10, scale=2)
     * @GRID\Column(title="Monto", visible=true, filterable=false, type="number", align="right", style="decimal", precision=2)
     */
    private $monto;

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
     * @var string
     *
     * @ORM\Column(name="UsuaModi", type="string", length=20, nullable=true)
     */
    private $usuaModi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaModi", type="datetime", nullable=true)
     */
    private $fechaModi;


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
     * @ORM\ManyToOne(targetEntity="CuposHATiposLiquidacion", inversedBy="Cupos")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCupo")
     */
    
    /**
     * @ORM\ManyToOne(targetEntity="Vacantes", inversedBy="Cupos")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCupo")
     */
    
    /**
     * @ORM\ManyToOne(targetEntity="FechaCierre", inversedBy="Cupos")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCupo")
     */
    
    
    /**
     * Set RefCupoEstado
     *
     * @param integer $RefCupoEstado
     * @return Cupos
     */
    public function setRefCupoEstado($RefCupoEstado)
    {
        $this->RefCupoEstado = $RefCupoEstado;

        return $this;
    }
    
    
    /**
     * Set refCupoAnual
     *
     * @param integer $refCupoAnual
     * @return Cupos
     */
    public function setRefCupoAnual($refCupoAnual)
    {
        $this->refCupoAnual = $refCupoAnual;

        return $this;
    }

    /**
     * Get refCupoAnual
     *
     * @return integer 
     */
    public function getRefCupoAnual()
    {
        return $this->refCupoAnual;
    }
    
    
    
    /**
     * Set idDependencia
     *
     * @param integer $idDependencia
     * @return Cupos
     */
    public function setIdDependencia($idDependencia)
    {
        $this->idDependencia = $idDependencia;

        return $this;
    }

    /**
     * Get idDependencia
     *
     * @return integer 
     */
    public function getIdDependencia()
    {
        return $this->idDependencia;
    }



    /**
     * Set mes
     *
     * @param integer $mes
     * @return Cupos
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return integer 
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set anio
     *
     * @param integer $anio
     * @return Cupos
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return integer 
     */
    public function getAnio()
    {
        return $this->anio;
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
     * Set usuaCrea
     *
     * @param string $usuaCrea
     * @return Cupos
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
     * @return Cupos
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
     * Set usuaModi
     *
     * @param string $usuaModi
     * @return Cupos
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
     * Set fechaModi
     *
     * @param \DateTime $fechaModi
     * @return Cupos
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
     * @ORM\ManyToOne(targetEntity="CupoEstados")
     * @ORM\JoinColumn(name="RefCupoEstado", referencedColumnName="id", nullable=false)
     * @GRID\Column(field="cupoestados.cupoEstado", type="text", title="Estado")
     */
    protected $cupoestados;
    
    /**
     * @ORM\ManyToOne(targetEntity="CuposAnuales")
     * @ORM\JoinColumn(name="RefCupoAnual", referencedColumnName="id", nullable=false)
     * @GRID\Column(field="cuposanuales.descripcion", title="CupoAnual", visible=true,  operatorsVisible=true)
     */
    protected $cuposanuales;

    public function __construct() {

        $this->cupoestados = new ArrayCollection();
        $this->cuposanuales = new ArrayCollection();
    }

    /**
     * Set dependencias
     *
     * @param \Liquidaciones\ReferenciasBundle\Entity\Dependencias $dependencias
     * @return Dependencias
     */
    //public function setDependencias(\Liquidaciones\ReferenciasBundle\Entity\Dependencias $dependencias=null)
    //{
    //    $this->dependencias = $dependencias;

    //    return $this;
    //}

    /**
     * Get dependencias
     *
     * @return \Liquidaciones\ReferenciasBundle\Entity\Dependencias 
     */
    //public function getDependencias()
    //{
    //    return $this->dependencias;
    //}
    
    /**
     * Set cupoestado
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\CupoEstados $cupoestados
     * @return Cupos
     */
    public function setCupoestado(\Liquidaciones\CuposAnualesBundle\Entity\CupoEstados $cupoestados=null)
    {
        $this->cupoestados = $cupoestados;

        return $this;
    }

    /**
     * Get cupoestado
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\CupoEstados 
     */
    public function getCupoestado()
    {
        return $this->cupoestados;
    }
    
    
    /**
     * Set cuposanuales
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\CuposAnuales $cuposanuales
     * @return Cupos
     */
    public function setCuposanuales(\Liquidaciones\CuposAnualesBundle\Entity\CuposAnuales $cuposanuales=null)
    {
        $this->cuposanuales = $cuposanuales;

        return $this;
    }

    /**
     * Get cuposanuales
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\CuposAnuales 
     */
    public function getCuposanuales()
    {
        return $this->cuposanuales;
    }

   

    public function __toString()
    {
        return $this->getCuposanuales()->getDescripcion().' - '.$this->getAnio().'/'.$this->getMes().' Depe: '.$this->getIdDependencia();
    }


}
