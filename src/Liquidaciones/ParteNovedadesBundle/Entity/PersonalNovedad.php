<?php

namespace MinSaludBA\ParteNovedadesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * PersonalNovedad
 *
 * @ORM\Table(name="Haberes.Personal.PersonalNovedad")
 * @ORM\Entity
 * @GRID\Source(columns="IdPersonalNovedad, Novedad.novedad, FechaDesde, FechaHasta, Eliminado")
 */
class PersonalNovedad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IdPersonalNovedad", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Plantel", inversedBy="parteNovedades")
     * @ORM\JoinColumn(name="RefPersonalCargo", referencedColumnName="IdPersonalCargo")
     */
    private $Plantel;
    
    /**
     * @ORM\ManyToOne(targetEntity="Novedad")
     * @ORM\JoinColumn(name="RefNovedad", referencedColumnName="id")
     * @GRID\Column(field="Novedad.novedad", type="text", title="Novedad", filterable=true, filter="select", operatorsVisible=false)
     */
    private $Novedad;

    /**
     * @var \Date
     *
     * @ORM\Column(name="FechaDesde", type="date")
     */
    private $FechaDesde;

    /**
     * @var \Date
     *
     * @ORM\Column(name="FechaHasta", type="date")
     */
    private $FechaHasta;

    /**
     * @var integer
     *
     * @ORM\Column(name="CantidadMinutosPorDia", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $CantidadMinutosPorDia;

    /**
     * @var string
     *
     * @ORM\Column(name="Observaciones", type="string", length=255)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $Observaciones;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Eliminado", type="boolean")
     */
    private $Eliminado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Fecha_Modi", type="datetime")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $Fecha_Modi;

    /**
     * @var string
     *
     * @ORM\Column(name="Usua_Modi", type="string", length=30)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $Usua_Modi;

    /**
     * @var integer
     *
     * @ORM\Column(name="Depe_Modi", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $Depe_Modi;

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
     * Set Plantel
     */
    public function setPlantel(Plantel $plantel)
    {
        $this->Plantel = $plantel;
    
        return $this;
    }

    /**
     * Get Plantel
     */
    public function getPlantel()
    {
        return $this->Plantel;
    }

    /**
     * Set Novedad
     */
    public function setNovedad(Novedad $novedad)
    {
        $this->Novedad = $novedad;
    
        return $this;
    }

    /**
     * Get Novedad
     */
    public function getNovedad()
    {
        return $this->Novedad;
    }

    /**
     * Set FechaDesde
     *
     * @param \Date $fechaDesde
     * @return PersonalNovedad
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->FechaDesde = $fechaDesde;
    
        return $this;
    }

    /**
     * Get FechaDesde
     *
     * @return \Date
     */
    public function getFechaDesde()
    {
        return $this->FechaDesde;
    }

    /**
     * Set FechaHasta
     *
     * @param \Date $fechaHasta
     * @return PersonalNovedad
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->FechaHasta = $fechaHasta;
    
        return $this;
    }

    /**
     * Get FechaHasta
     *
     * @return \Date
     */
    public function getFechaHasta()
    {
        return $this->FechaHasta;
    }

    /**
     * Set CantidadMinutosPorDia
     *
     * @param integer $cantidadMinutosPorDia
     * @return PersonalNovedad
     */
    public function setCantidadMinutosPorDia($cantidadMinutosPorDia)
    {
        $this->CantidadMinutosPorDia = $cantidadMinutosPorDia;
    
        return $this;
    }

    /**
     * Get CantidadMinutosPorDia
     *
     * @return integer 
     */
    public function getCantidadMinutosPorDia()
    {
        return $this->CantidadMinutosPorDia;
    }

    /**
     * Set Observaciones
     *
     * @param string $observaciones
     * @return PersonalNovedad
     */
    public function setObservaciones($observaciones)
    {
        $this->Observaciones = $observaciones;
    
        return $this;
    }

    /**
     * Get Observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->Observaciones;
    }

    /**
     * Set Eliminado
     *
     * @param boolean $eliminado
     * @return PersonalNovedad
     */
    public function setEliminado($eliminado)
    {
        $this->Eliminado = $eliminado;
    
        return $this;
    }

    /**
     * Get Eliminado
     *
     * @return boolean 
     */
    public function getEliminado()
    {
        return $this->Eliminado;
    }

    /**
     * Set Fecha_Modi
     *
     * @param \DateTime $fechaModi
     * @return PersonalNovedad
     */
    public function setFechaModi($fechaModi)
    {
        $this->Fecha_Modi = $fechaModi;
    
        return $this;
    }

    /**
     * Get Fecha_Modi
     *
     * @return \DateTime 
     */
    public function getFechaModi()
    {
        return $this->Fecha_Modi;
    }

    /**
     * Set Usua_Modi
     *
     * @param string $usuaModi
     * @return PersonalNovedad
     */
    public function setUsuaModi($usuaModi)
    {
        $this->Usua_Modi = $usuaModi;
    
        return $this;
    }

    /**
     * Get Usua_Modi
     *
     * @return string 
     */
    public function getUsuaModi()
    {
        return $this->Usua_Modi;
    }

    /**
     * Set Depe_Modi
     *
     * @param integer $depeModi
     * @return PersonalNovedad
     */
    public function setDepeModi($depeModi)
    {
        $this->Depe_Modi = $depeModi;
    
        return $this;
    }

    /**
     * Get Depe_Modi
     *
     * @return integer 
     */
    public function getDepeModi()
    {
        return $this->Depe_Modi;
    }
    
    public function __construct()
    {
        $this->Fecha_Modi = new \DateTime();
    }
}
