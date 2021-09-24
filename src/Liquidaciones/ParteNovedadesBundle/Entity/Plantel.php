<?php

namespace Liquidaciones\ParteNovedadesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Plantel
 * 
 * @ORM\Table(name="Haberes.Personal.vwPlantel")
 * @ORM\Entity
 * 
 * @GRID\Source(columns="IdPersonalCargo, IdPersona, Legajo, Apellido, Nombre, IdParte, IdRegimenEstatutario, IdAgrupamiento, Categoria, IdDestinoCobro, IdDependenciaPlantel, RegHorario, Guardia, Funcion, Periodo, FechaBajaPreventiva, Antiguedad")
 */
class Plantel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IdPersonalCargo", type="integer")
     * @ORM\Id
     * 
     * @GRID\Column(visible=false, filterable=false)
     */
    private $IdPersonalCargo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdPersona", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $IdPersona;

    /**
     * @var integer
     *
     * @ORM\Column(name="Legajo", type="integer")
     * @GRID\Column(title="Legajo", visible=true, filterable=true)
     */
    private $Legajo;

    /**
     * @var string
     *
     * @ORM\Column(name="Apellido", type="string", length=255)
     * @GRID\Column(title="Apellido", visible=true, filterable=true)
     */
    private $Apellido;

    /**
     * @var string
     *
     * @ORM\Column(name="Nombre", type="string", length=255)
     * @GRID\Column(title="Nombre", visible=true, filterable=true)
     */
    private $Nombre;

    /**
     * @ORM\ManyToOne(targetEntity="Partes")
     * @ORM\JoinColumn(name="IdParte", referencedColumnName="IdParte")
     * @GRID\Column(field="Parte.ParteDescripcion", type="text", title="Parte", filterable=true, filter="select", operatorsVisible=false)
     */
    private $Parte;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdRegimenEstatutario", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $IdRegimenEstatutario;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdAgrupamiento", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $IdAgrupamiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="Categoria", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $Categoria;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdDestinoCobro", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $IdDestinoCobro;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdDependenciaPlantel", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $IdDependenciaPlantel;

    /**
     * @var integer
     *
     * @ORM\Column(name="RegHorario", type="integer")
     * @GRID\Column(visible=true, filterable=false)
     */
    private $RegHorario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="Guardia", type="boolean")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $Guardia;

    /**
     * @var string
     *
     * @ORM\Column(name="Funcion", type="string", length=255)
     * @GRID\Column(title="Cargo/Función", visible=true, filterable=false, type="text")
     */
    private $Funcion;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Periodo", type="date")
     * @GRID\Column(visible=false, filterable=false)
     *      */
    private $Periodo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaBajaPreventiva", type="date")
     * 
     * * @GRID\Column(visible=true, filterable=false)
     */
    private $FechaBajaPreventiva;

    /**
     * @var integer
     *
     * @ORM\Column(name="Antiguedad", type="integer")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $Antiguedad;
    
    /**
     * Set IdPersonalCargo
     *
     * @param integer $idPersonalCargo
     * @return Plantel
     */
    public function setIdPersonalCargo($idPersonalCargo)
    {
        $this->IdPersonalCargo = $idPersonalCargo;
    
        return $this;
    }

    /**
     * Get IdPersonalCargo
     *
     * @return integer 
     */
    public function getIdPersonalCargo()
    {
        return $this->IdPersonalCargo;
    }

    /**
     * Set IdPersonal
     *
     * @param integer $idPersonal
     * @return Plantel
     */
    public function setIdPersona($idPersona)
    {
        $this->IdPersona = $idPersona;
    
        return $this;
    }

    /**
     * Get IdPersonal
     *
     * @return integer 
     */
    public function getIdPersona()
    {
        return $this->IdPersona;
    }

    /**
     * Set Legajo
     *
     * @param integer $legajo
     * @return Plantel
     */
    public function setLegajo($legajo)
    {
        $this->Legajo = $legajo;
    
        return $this;
    }

    /**
     * Get Legajo
     *
     * @return integer 
     */
    public function getLegajo()
    {
        return $this->Legajo;
    }

    /**
     * Set Apellido
     *
     * @param string $apellido
     * @return Plantel
     */
    public function setApellido($apellido)
    {
        $this->Apellido = $apellido;
    
        return $this;
    }

    /**
     * Get Apellido
     *
     * @return string 
     */
    public function getApellido()
    {
        return $this->Apellido;
    }

    /**
     * Set Nombre
     *
     * @param string $nombre
     * @return Plantel
     */
    public function setNombre($nombre)
    {
        $this->Nombre = $nombre;
    
        return $this;
    }

    /**
     * Get Nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->Nombre;
    }

    /**
     * Set IdParte
     *
     * @param integer $idParte
     * @return Plantel
     */
    public function setIdParte($idParte)
    {
        $this->IdParte = $idParte;
    
        return $this;
    }

    /**
     * Get IdParte
     *
     * @return integer 
     */
    public function getIdParte()
    {
        return $this->IdParte;
    }

    /**
     * Set IdRegimenEstatutario
     *
     * @param integer $idRegimenEstatutario
     * @return Plantel
     */
    public function setIdRegimenEstatutario($idRegimenEstatutario)
    {
        $this->IdRegimenEstatutario = $idRegimenEstatutario;
    
        return $this;
    }

    /**
     * Get IdRegimenEstatutario
     *
     * @return integer 
     */
    public function getIdRegimenEstatutario()
    {
        return $this->IdRegimenEstatutario;
    }

    /**
     * Set IdAgrupamiento
     *
     * @param integer $idAgrupamiento
     * @return Plantel
     */
    public function setIdAgrupamiento($idAgrupamiento)
    {
        $this->IdAgrupamiento = $idAgrupamiento;
    
        return $this;
    }

    /**
     * Get IdAgrupamiento
     *
     * @return integer 
     */
    public function getIdAgrupamiento()
    {
        return $this->IdAgrupamiento;
    }

    /**
     * Set Categoria
     *
     * @param integer $categoria
     * @return Plantel
     */
    public function setCategoria($categoria)
    {
        $this->Categoria = $categoria;
    
        return $this;
    }

    /**
     * Get Categoria
     *
     * @return integer 
     */
    public function getCategoria()
    {
        return $this->Categoria;
    }

    /**
     * Set IdDestinoCobro
     *
     * @param integer $idDestinoCobro
     * @return Plantel
     */
    public function setIdDestinoCobro($idDestinoCobro)
    {
        $this->IdDestinoCobro = $idDestinoCobro;
    
        return $this;
    }

    /**
     * Get IdDestinoCobro
     *
     * @return integer 
     */
    public function getIdDestinoCobro()
    {
        return $this->IdDestinoCobro;
    }

    /**
     * Set IdDependenciaPlantel
     *
     * @param integer $idDependenciaPlantel
     * @return Plantel
     */
    public function setIdDependenciaPlantel($idDependenciaPlantel)
    {
        $this->IdDependenciaPlantel = $idDependenciaPlantel;
    
        return $this;
    }

    /**
     * Get IdDependenciaPlantel
     *
     * @return integer 
     */
    public function getIdDependenciaPlantel()
    {
        return $this->IdDependenciaPlantel;
    }

    /**
     * Set RegHorario
     *
     * @param integer $regHorario
     * @return Plantel
     */
    public function setRegHorario($regHorario)
    {
        $this->RegHorario = $regHorario;
    
        return $this;
    }

    /**
     * Get RegHorario
     *
     * @return integer 
     */
    public function getRegHorario()
    {
        return $this->RegHorario;
    }

    /**
     * Set Guardia
     *
     * @param boolean $guardia
     * @return Plantel
     */
    public function setGuardia($guardia)
    {
        $this->Guardia = $guardia;
    
        return $this;
    }

    /**
     * Get Guardia
     *
     * @return boolean 
     */
    public function getGuardia()
    {
        return $this->Guardia;
    }

    /**
     * Set Funcion
     *
     * @param string $funcion
     * @return Plantel
     */
    public function setFuncion($funcion)
    {
        $this->Funcion = $funcion;
    
        return $this;
    }

    /**
     * Get Funcion
     *
     * @return string 
     */
    public function getFuncion()
    {
        return $this->Funcion;
    }
    
    /**
     * Set Periodo
     *
     * @param \DateTime $periodo
     * @return Plantel
     */
    public function setPeriodo($periodo)
    {
        $this->Periodo = $periodo;
    
        return $this;
    }

    /**
     * Get Periodo
     *
     * @return \DateTime 
     */
    public function getPeriodo()
    {
        return $this->Periodo;
    }

    /**
     * Set FechaBajaPreventiva
     *
     * @param \DateTime $fechaBajaPreventiva
     * @return Plantel
     */
    public function setFechaBajaPreventiva($fechaBajaPreventiva)
    {
        $this->FechaBajaPreventiva = $fechaBajaPreventiva;
    
        return $this;
    }

    /**
     * Get FechaBajaPreventiva
     *
     * @return \DateTime 
     */
    public function getFechaBajaPreventiva()
    {
        return $this->FechaBajaPreventiva;
    }

    /**
     * Set Antiguedad
     *
     * @param integer $antiguedad
     * @return Plantel
     */
    public function setAntiguedad($antiguedad)
    {
        $this->Antiguedad = $antiguedad;
    
        return $this;
    }

    /**
     * Get Antiguedad
     *
     * @return integer 
     */
    public function getAntiguedad()
    {
        return $this->Antiguedad;
    }
    
    /**
     * @ORM\OneToMany(targetEntity="PersonalNovedad", mappedBy="Plantel")
     */
    private $personalNovedades;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Partes = new ArrayCollection();
        $this->personalNovedades = new ArrayCollection();
    }
}
