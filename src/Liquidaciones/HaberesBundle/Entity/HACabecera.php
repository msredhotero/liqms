<?php

namespace Liquidaciones\HaberesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * HACabecera
 *
 * @ORM\Table("haberes.Preliquidacion.HACabecera")
 * @ORM\Entity(repositoryClass="Liquidaciones\HaberesBundle\Entity\HACabeceraRepository")
 */
class HACabecera 
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IdCabecera", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="RefEscenario", type="integer")
     */
    private $refEscenario;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="RefTipoLiquidacion", type="integer")
     */
    private $refTipoLiquidacion;
    
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="Adicional", type="integer")
     */
    private $adicional;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="RefCabeceraCopia", type="integer")
     */
    private $refCabeceraCopia;
    
    
    /**
     * @var date
     *
     * @ORM\Column(name="Periodo", type="date")
     */
    private $periodo;
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="Preliquidar", type="string")
     */
    private $preliquidar;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="Bloqueado", type="string")
     */
    private $bloqueado;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="Observaciones", type="string")
     */
    private $observaciones;
    
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="Fecha_Modi", type="datetime")
     */
    private $fechaModi;
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="Usua_Modi", type="string")
     */
    private $usuaModi;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="EsPrivada", type="string")
     */
    private $esPrivada;
    
    
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
     * Set refEscenario
     *
     * @param integer $refEscenario
     * @return HACabecera
     */
    public function setRefEscenario($refEscenario)
    {
        $this->refEscenario = $refEscenario;

        return $this;
    }

    /**
     * Get refEscenario
     *
     * @return integer 
     */
    public function getRefEscenario()
    {
        return $this->refEscenario;
    }
    
    
    
    /**
     * Set refTipoLiquidacion
     *
     * @param integer $refTipoLiquidacion
     * @return HACabecera
     */
    public function setRefTipoLiquidacion($refTipoLiquidacion)
    {
        $this->refTipoLiquidacion = $refTipoLiquidacion;

        return $this;
    }

    /**
     * Get refTipoLiquidacion
     *
     * @return integer 
     */
    public function getRefTipoLiquidacion()
    {
        return $this->refTipoLiquidacion;
    }
    
    
    
    /**
     * Set adicional
     *
     * @param integer $adicional
     * @return HACabecera
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
     * Set refCabeceraCopia
     *
     * @param integer $refCabeceraCopia
     * @return HACabecera
     */
    public function setRefCabeceraCopia($refCabeceraCopia)
    {
        $this->refCabeceraCopia = $refCabeceraCopia;

        return $this;
    }

    /**
     * Get refCabeceraCopia
     *
     * @return integer 
     */
    public function getRefCabeceraCopia()
    {
        return $this->refCabeceraCopia;
    }
    
    
    
    /**
     * Set periodo
     *
     * @param date $periodo
     * @return HACabecera
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return date 
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }
    
    
    
    /**
     * Set bloqueado
     *
     * @param string $bloqueado
     * @return HACabecera
     */
    public function setBloqueado($bloqueado)
    {
        $this->bloqueado = $bloqueado;

        return $this;
    }

    /**
     * Get bloqueado
     *
     * @return string 
     */
    public function getBloqueado()
    {
        return $this->bloqueado;
    }
    
    
    /**
     * Set observaciones
     *
     * @param string $observaciones
     * @return HACabecera
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }
    
    
    
    /**
     * Set fechaModi
     *
     * @param \DateTime $fechaModi
     * @return HACabecera
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
     * Set usuaModi
     *
     * @param string $usuaModi
     * @return HACabecera
     */
    public function setUsuaModi($usuaModi)
    {
        $this->usuaModi = $usuaModi;

        return $this;
    }

    /**
     * Get usuaModi
     *
     * @return string 
     */
    public function getUsuaModi()
    {
        return $this->usuaModi;
    }
    
    
    
    /**
     * Set esPrivada
     *
     * @param string $esPrivada
     * @return HACabecera
     */
    public function setEsPrivada($esPrivada)
    {
        $this->esPrivada = $esPrivada;

        return $this;
    }

    /**
     * Get esPrivada
     *
     * @return string 
     */
    public function getEsPrivada()
    {
        return $this->esPrivada;
    }
    
    
    /**
     * @ORM\ManyToOne(targetEntity="HATiposLiquidacion")
     * @ORM\JoinColumn(name="RefTipoLiquidacion", referencedColumnName="IdTipoLiquidacion", nullable=false)
     */
    protected $tiposliquidacion;
    
    public function __construct() {

        $this->tiposliquidacion = new ArrayCollection();
    }
    
    
    /**
     * Set tiposliquidacion
     *
     * @param \Liquidaciones\HaberesBundle\Entity\HATiposLiquidacion $tiposliquidacion
     * @return HACabecera
     */
    public function setTiposliquidacion(\Liquidaciones\HaberesBundle\Entity\HATiposLiquidacion $tiposliquidacion=null)
    {
        $this->tiposliquidacion = $tiposliquidacion;

        return $this;
    }

    /**
     * Get tiposliquidacion
     *
     * @return \Liquidaciones\HaberesBundle\Entity\HATiposLiquidacion 
     */
    public function getTiposliquidacion()
    {
        return $this->tiposliquidacion;
    }
}
