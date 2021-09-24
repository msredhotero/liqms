<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Cuentas
 *
 * @ORM\Table("LiquidacionesWeb.dbo.cuentas")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuentasRepository")
 */
class Cuentas
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
     * @ORM\Column(name="ConceptoMS", type="smallint", nullable=true)
     */
    private $conceptoMS;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdTipoLiquidacion", type="integer", nullable=true)
     */
    private $idTipoLiquidacion;

    /**
     * @var string
     *
     * @ORM\Column(name="Cuenta", type="string", length=255)
     * @GRID\Column(title="Cuenta", visible=true,  operatorsVisible=true)
     */
    private $cuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="Activo", type="string", length=1)
     */
    private $activo;

    /**
     * @var string
     *
     * @ORM\Column(name="ModoCarga", type="string", length=5)
     */
    private $modoCarga;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="EsPresupuestaria", type="string", length=1, nullable=true)
     */
    private $esPresupuestaria;

    /**
     * @var string
     *
     * @ORM\Column(name="CodOperacion", type="string", length=4, nullable=true)
     */
    private $codOperacion;

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
     * @ORM\ManyToOne(targetEntity="CuposHATiposLiquidacion", inversedBy="Cuentas")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCuenta")
     */
    
    /**
     * @ORM\ManyToOne(targetEntity="CuentasConceptos", inversedBy="Cuentas")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCuenta")
     */
    
    
    
    /**
     * Set conceptoMS
     *
     * @param integer $conceptoMS
     * @return Cuentas
     */
    public function setConceptoMS($conceptoMS)
    {
        $this->conceptoMS = $conceptoMS;

        return $this;
    }

    /**
     * Get conceptoMS
     *
     * @return integer 
     */
    public function getConceptoMS()
    {
        return $this->conceptoMS;
    }

    /**
     * Set idTipoLiquidacion
     *
     * @param integer $idTipoLiquidacion
     * @return Cuentas
     */
    public function setIdTipoLiquidacion($idTipoLiquidacion)
    {
        $this->idTipoLiquidacion = $idTipoLiquidacion;

        return $this;
    }

    /**
     * Get idTipoLiquidacion
     *
     * @return integer 
     */
    public function getIdTipoLiquidacion()
    {
        return $this->idTipoLiquidacion;
    }

    /**
     * Set cuenta
     *
     * @param string $cuenta
     * @return Cuentas
     */
    public function setCuenta($cuenta)
    {
        $this->cuenta = utf8_decode($cuenta);

        return $this;
    }

    /**
     * Get cuenta
     *
     * @return string 
     */
    public function getCuenta()
    {
        return utf8_encode($this->cuenta);
    }

    /**
     * Set activo
     *
     * @param string $activo
     * @return Cuentas
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

    /**
     * Set modoCarga
     *
     * @param string $modoCarga
     * @return Cuentas
     */
    public function setModoCarga($modoCarga)
    {
        $this->modoCarga = $modoCarga;

        return $this;
    }

    /**
     * Get modoCarga
     *
     * @return string 
     */
    public function getModoCarga()
    {
        return $this->modoCarga;
    }
    
    
    /**
     * Set esPresupuestaria
     *
     * @param string $esPresupuestaria
     * @return Cuentas
     */
    public function setEsPresupuestaria($esPresupuestaria)
    {
        $this->esPresupuestaria = $esPresupuestaria;

        return $this;
    }

    /**
     * Get esPresupuestaria
     *
     * @return string 
     */
    public function getEsPresupuestaria()
    {
        return $this->esPresupuestaria;
    }

    /**
     * Set codOperacion
     *
     * @param string $codOperacion
     * @return Cuentas
     */
    public function setCodOperacion($codOperacion)
    {
        $this->codOperacion = $codOperacion;

        return $this;
    }

    /**
     * Get codOperacion
     *
     * @return string 
     */
    public function getCodOperacion()
    {
        return $this->codOperacion;
    }
    
    
    public function __toString() {
        return utf8_encode($this->getCuenta());
    }
    
}
