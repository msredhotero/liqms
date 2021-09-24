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
 * ValidacionModulos
 *
 * @ORM\Table("LiquidacionesWeb.dbo.ValidacionModulos")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuposRepository")
 */
class ValidacionModulos
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
     * @var int
     *
     * @ORM\Column(name="refregimenestatutario", type="integer", nullable=false)
     */
    private $refregimenestatutario;

    /**
     * @var int
     *
     * @ORM\Column(name="refagrupamiento", type="integer", nullable=false)
     */
    private $refagrupamiento;

    /**
     * @var int
     *
     * @ORM\Column(name="refencasillamiento", type="integer", nullable=false)
     */
    private $refencasillamiento;

    


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
     * Set refregimenestatutario
     *
     * @param integer $refregimenestatutario
     * @return TomaPosesion
     */
    public function setRefregimenestatutario($refregimenestatutario)
    {
        $this->refregimenestatutario = $refregimenestatutario;

        return $this;
    }

    /**
     * Get refregimenestatutario
     *
     * @return integer 
     */
    public function getRefregimenestatutario()
    {
        return $this->refregimenestatutario;
    }

    /**
     * Set refagrupamiento
     *
     * @param integer $refagrupamiento
     * @return refagrupamiento
     */
    public function setRefagrupamiento($refagrupamiento)
    {
        $this->refagrupamiento = $refagrupamiento;

        return $this;
    }

    /**
     * Get refagrupamiento
     *
     * @return integer 
     */
    public function getRefagrupamiento()
    {
        return $this->refagrupamiento;
    }

    /**
     * Set refencasillamiento
     *
     * @param integer $refencasillamiento
     * @return refencasillamiento
     */
    public function setRefencasillamiento($refencasillamiento)
    {
        $this->refencasillamiento = $refencasillamiento;

        return $this;
    }

    /**
     * Get refencasillamiento
     *
     * @return refencasillamiento 
     */
    public function getRefencasillamiento()
    {
        return $this->refencasillamiento;
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
    


}
