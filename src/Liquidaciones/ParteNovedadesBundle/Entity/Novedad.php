<?php

namespace Liquidaciones\ParteNovedadesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Novedad
 *
 * @ORM\Table(name="partenovedades.dbo.novedad")
 * @ORM\Entity
 */
class Novedad
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="novedad", type="string", length=255)
     * @GRID\Column(visible=true, filterable=true, title="Nombre", filter="select", operatorsVisible=false)
     */
    private $novedad;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=3)
     * @GRID\Column(visible=true, filterable=false, title="Código", filter="select", operatorsVisible=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcionArt", type="text")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $descripcionArt;

    /**
     * @var string
     *
     * @ORM\Column(name="baja", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $baja;

    /**
     * @var string
     *
     * @ORM\Column(name="descuento", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $descuento;

    /**
     * @var string
     *
     * @ORM\Column(name="suspende", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $suspende;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario_modif", type="string", length=8)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $usuario_modif;

    /**
     * @var string
     *
     * @ORM\Column(name="exceptGuardia", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $exceptGuardia;

    /**
     * @var string
     *
     * @ORM\Column(name="exceptPlanta", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $exceptPlanta;

    /**
     * @var string
     *
     * @ORM\Column(name="cont1Planta", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $cont1Planta;

    /**
     * @var string
     *
     * @ORM\Column(name="cont2Planta", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $cont2Planta;
    
    /**
     * @var string
     *
     * @ORM\Column(name="requiereExpediente", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $requiereExpediente;
    
    /**
     * @var string
     *
     * @ORM\Column(name="requiereCantMinutos", type="string", length=1)
     * @GRID\Column(visible=false, filterable=false)
     */
    private $requiereCantMinutos;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoDeNovedad", type="string", length=1)
     * @GRID\Column(visible=true, filterable=true, title="Tipo", filter="select", operatorsVisible=false)
     */
    private $tipoDeNovedad;

    /**
     * @var string
     *
     * @ORM\Column(name="vigente", type="string", length=1)
     * @GRID\Column(visible=true, filterable=false, title="Vigente", filter="select", operatorsVisible=false)
     */
    private $vigente;
    
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
     * Set novedad
     *
     * @param string $novedad
     * @return Novedad
     */
    public function setNovedad($novedad)
    {
        $this->novedad = $novedad;
    
        return $this;
    }

    /**
     * Get novedad
     *
     * @return string 
     */
    public function getNovedad()
    {
        return $this->novedad;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return Novedad
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    
        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set descripcionArt
     *
     * @param string $descripcionArt
     * @return Novedad
     */
    public function setDescripcionArt($descripcionArt)
    {
        $this->descripcionArt = $descripcionArt;
    
        return $this;
    }

    /**
     * Get descripcionArt
     *
     * @return string 
     */
    public function getDescripcionArt()
    {
        return $this->descripcionArt;
    }

    /**
     * Set diasMax
     *
     * @param integer $diasMax
     * @return Novedad
     */
    public function setDiasMax($diasMax)
    {
        $this->diasMax = $diasMax;
    
        return $this;
    }

    /**
     * Get diasMax
     *
     * @return integer 
     */
    public function getDiasMax()
    {
        return $this->diasMax;
    }

    /**
     * Set requiereExpediente
     *
     * @param string $requiereExpediente
     * @return Novedad
     */
    public function setRequiereExpediente($requiereExpediente)
    {
        $this->requiereExpediente = $requiereExpediente;
    
        return $this;
    }

    /**
     * Get requiereExpediente
     *
     * @return string 
     */
    public function getRequiereExpediente()
    {
        return $this->requiereExpediente;
    }

    /**
     * Set baja
     *
     * @param string $baja
     * @return Novedad
     */
    public function setBaja($baja)
    {
        $this->baja = $baja;
    
        return $this;
    }

    /**
     * Get baja
     *
     * @return string 
     */
    public function getBaja()
    {
        return $this->baja;
    }

    /**
     * Set descuento
     *
     * @param string $descuento
     * @return Novedad
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
    
        return $this;
    }

    /**
     * Get descuento
     *
     * @return string 
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set suspende
     *
     * @param string $suspende
     * @return Novedad
     */
    public function setSuspende($suspende)
    {
        $this->suspende = $suspende;
    
        return $this;
    }

    /**
     * Get suspende
     *
     * @return string 
     */
    public function getSuspende()
    {
        return $this->suspende;
    }

    /**
     * Set usuario_modif
     *
     * @param string $usuarioModif
     * @return Novedad
     */
    public function setUsuarioModif($usuarioModif)
    {
        $this->usuario_modif = $usuarioModif;
    
        return $this;
    }

    /**
     * Get usuario_modif
     *
     * @return string 
     */
    public function getUsuarioModif()
    {
        return $this->usuario_modif;
    }

    /**
     * Set exceptGuardia
     *
     * @param string $exceptGuardia
     * @return Novedad
     */
    public function setExceptGuardia($exceptGuardia)
    {
        $this->exceptGuardia = $exceptGuardia;
    
        return $this;
    }

    /**
     * Get exceptGuardia
     *
     * @return string 
     */
    public function getExceptGuardia()
    {
        return $this->exceptGuardia;
    }

    /**
     * Set exceptPlanta
     *
     * @param string $exceptPlanta
     * @return Novedad
     */
    public function setExceptPlanta($exceptPlanta)
    {
        $this->exceptPlanta = $exceptPlanta;
    
        return $this;
    }

    /**
     * Get exceptPlanta
     *
     * @return string 
     */
    public function getExceptPlanta()
    {
        return $this->exceptPlanta;
    }

    /**
     * Set cont1Planta
     *
     * @param string $cont1Planta
     * @return Novedad
     */
    public function setCont1Planta($cont1Planta)
    {
        $this->cont1Planta = $cont1Planta;
    
        return $this;
    }

    /**
     * Get cont1Planta
     *
     * @return string 
     */
    public function getCont1Planta()
    {
        return $this->cont1Planta;
    }

    /**
     * Set cont2Planta
     *
     * @param string $cont2Planta
     * @return Novedad
     */
    public function setCont2Planta($cont2Planta)
    {
        $this->cont2Planta = $cont2Planta;
    
        return $this;
    }

    /**
     * Get cont2Planta
     *
     * @return string 
     */
    public function getCont2Planta()
    {
        return $this->cont2Planta;
    }
    
     /**
     * Set requiereCantMinutos
     *
     * @param string $requiereCantMinutos
     * @return Novedad
     */
    public function setRequiereCantMinutos($requiereCantMinutos)
    {
        $this->requiereCantMinutos = $requiereCantMinutos;
    
        return $this;
    }

    /**
     * Get requiereCantMinutos
     *
     * @return string 
     */
    public function getRequiereCantMinutos()
    {
        return $this->requiereCantMinutos;
    }

    /**
     * Set tipoDeNovedad
     *
     * @param string $tipoDeNovedad
     * @return Novedad
     */
    public function setTipoDeNovedad($tipoDeNovedad)
    {
        $this->tipoDeNovedad = $tipoDeNovedad;
    
        return $this;
    }

    /**
     * Get tipoDeNovedad
     *
     * @return string 
     */
    public function getTipoDeNovedad()
    {
        return $this->tipoDeNovedad;
    }
    
    function __toString(){
        return $this->getCodigo() . ' - ' . $this->getNovedad();
    }

    /**
     * Set vigente
     *
     * @param string $vigente
     * @return Novedad
     */
    public function setVigente($vigente)
    {
        $this->vigente = $vigente;
    
        return $this;
    }

    /**
     * Get vigente
     *
     * @return string 
     */
    public function getVigente()
    {
        return $this->vigente;
    }
}