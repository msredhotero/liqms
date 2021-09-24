<?php

namespace Liquidaciones\ReferenciasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Dependencias
 *
 * @ORM\Table(name="trefe14.dependencias")
 * @ORM\Entity(repositoryClass="Liquidaciones\ReferenciasBundle\Entity\DependenciasRepository")
 */
class Dependencias
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
     * @var string
     *
     * @ORM\Column(name="Codigo", type="string", length=4)
     * @GRID\Column(title="Codigo", visible=true, operators={"like"},defaultOperator="like", operatorsVisible=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="Nombre", type="string", length=255)
     * @GRID\Column(title="Depe", visible=true, operators={"like"},defaultOperator="like", operatorsVisible=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="Estable_id", type="string", length=8)
     */
    private $estableId;

    /**
     * @var string
     *
     * @ORM\Column(name="Codigo_exte", type="string", length=7)
     */
    private $codigoExte;

    /**
     * @var integer
     *
     * @ORM\Column(name="Entecaratulador", type="integer")
     */
    private $entecaratulador;

    /**
     * @var string
     *
     * @ORM\Column(name="Domicilio", type="string", length=255)
     */
    private $domicilio;

    /**
     * @var string
     *
     * @ORM\Column(name="Telefono", type="string", length=50)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="Pais_id", type="string", length=3)
     */
    private $paisId;

    /**
     * @var string
     *
     * @ORM\Column(name="Prov_id", type="string", length=3)
     */
    private $provId;

    /**
     * @var string
     *
     * @ORM\Column(name="Partido_id", type="string", length=3)
     */
    private $partidoId;

    /**
     * @var string
     *
     * @ORM\Column(name="Locali_id", type="string", length=2)
     */
    private $localiId;

    /**
     * @var integer
     *
     * @ORM\Column(name="Tipo_jurisdiccion", type="integer")
     */
    private $tipoJurisdiccion;

    /**
     * @var integer
     *
     * @ORM\Column(name="Region_id", type="integer")
     */
    private $regionId;

    /**
     * @var string
     *
     * @ORM\Column(name="Email", type="string", length=100)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="Director", type="string", length=150)
     */
    private $director;

    /**
     * @var string
     *
     * @ORM\Column(name="Tipo_estable", type="string", length=2)
     */
    private $tipoEstable;

    /**
     * @var string
     *
     * @ORM\Column(name="Habilitado", type="string", length=1)
     */
    private $habilitado;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Fecha_modif", type="datetime")
     */
    private $fechaModif;

    /**
     * @var string
     *
     * @ORM\Column(name="Usu_modif", type="string", length=8)
     */
    private $usuModif;

    /**
     * @var string
     *
     * @ORM\Column(name="Codpost", type="string", length=10)
     */
    private $codpost;

    /**
     * @var string
     *
     * @ORM\Column(name="Aux_tipo_jurisdiccion", type="string", length=1)
     */
    private $auxTipoJurisdiccion;

    /**
     * @var integer
     *
     * @ORM\Column(name="Latitud", type="integer")
     */
    private $latitud;

    /**
     * @var integer
     *
     * @ORM\Column(name="Longitud", type="integer")
     */
    private $longitud;


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
     * Set codigo
     *
     * @param string $codigo
     * @return Dependencias
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
     * Set nombre
     *
     * @param string $nombre
     * @return Dependencias
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set estableId
     *
     * @param string $estableId
     * @return Dependencias
     */
    public function setEstableId($estableId)
    {
        $this->estableId = $estableId;

        return $this;
    }

    /**
     * Get estableId
     *
     * @return string 
     */
    public function getEstableId()
    {
        return $this->estableId;
    }

    /**
     * Set codigoExte
     *
     * @param string $codigoExte
     * @return Dependencias
     */
    public function setCodigoExte($codigoExte)
    {
        $this->codigoExte = $codigoExte;

        return $this;
    }

    /**
     * Get codigoExte
     *
     * @return string 
     */
    public function getCodigoExte()
    {
        return $this->codigoExte;
    }

    /**
     * Set entecaratulador
     *
     * @param integer $entecaratulador
     * @return Dependencias
     */
    public function setEntecaratulador($entecaratulador)
    {
        $this->entecaratulador = $entecaratulador;

        return $this;
    }

    /**
     * Get entecaratulador
     *
     * @return integer 
     */
    public function getEntecaratulador()
    {
        return $this->entecaratulador;
    }

    

    /**
     * Set domicilio
     *
     * @param string $domicilio
     * @return Dependencias
     */
    public function setDomicilio($domicilio)
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    /**
     * Get domicilio
     *
     * @return string 
     */
    public function getDomicilio()
    {
        return $this->domicilio;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     * @return Dependencias
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set paisId
     *
     * @param string $paisId
     * @return Dependencias
     */
    public function setPaisId($paisId)
    {
        $this->paisId = $paisId;

        return $this;
    }

    /**
     * Get paisId
     *
     * @return string 
     */
    public function getPaisId()
    {
        return $this->paisId;
    }

    /**
     * Set provId
     *
     * @param string $provId
     * @return Dependencias
     */
    public function setProvId($provId)
    {
        $this->provId = $provId;

        return $this;
    }

    /**
     * Get provId
     *
     * @return string 
     */
    public function getProvId()
    {
        return $this->provId;
    }

    /**
     * Set partidoId
     *
     * @param string $partidoId
     * @return Dependencias
     */
    public function setPartidoId($partidoId)
    {
        $this->partidoId = $partidoId;

        return $this;
    }

    /**
     * Get partidoId
     *
     * @return string 
     */
    public function getPartidoId()
    {
        return $this->partidoId;
    }

    /**
     * Set localiId
     *
     * @param string $localiId
     * @return Dependencias
     */
    public function setLocaliId($localiId)
    {
        $this->localiId = $localiId;

        return $this;
    }

    /**
     * Get localiId
     *
     * @return string 
     */
    public function getLocaliId()
    {
        return $this->localiId;
    }

    /**
     * Set tipoJurisdiccion
     *
     * @param integer $tipoJurisdiccion
     * @return Dependencias
     */
    public function setTipoJurisdiccion($tipoJurisdiccion)
    {
        $this->tipoJurisdiccion = $tipoJurisdiccion;

        return $this;
    }

    /**
     * Get tipoJurisdiccion
     *
     * @return integer 
     */
    public function getTipoJurisdiccion()
    {
        return $this->tipoJurisdiccion;
    }

    /**
     * Set regionId
     *
     * @param integer $regionId
     * @return Dependencias
     */
    public function setRegionId($regionId)
    {
        $this->regionId = $regionId;

        return $this;
    }

    /**
     * Get regionId
     *
     * @return integer 
     */
    public function getRegionId()
    {
        return $this->regionId;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Dependencias
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Dependencias
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * Get director
     *
     * @return string 
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set tipoEstable
     *
     * @param string $tipoEstable
     * @return Dependencias
     */
    public function setTipoEstable($tipoEstable)
    {
        $this->tipoEstable = $tipoEstable;

        return $this;
    }

    /**
     * Get tipoEstable
     *
     * @return string 
     */
    public function getTipoEstable()
    {
        return $this->tipoEstable;
    }

    /**
     * Set habilitado
     *
     * @param string $habilitado
     * @return Dependencias
     */
    public function setHabilitado($habilitado)
    {
        $this->habilitado = $habilitado;

        return $this;
    }

    /**
     * Get habilitado
     *
     * @return string 
     */
    public function getHabilitado()
    {
        return $this->habilitado;
    }

    /**
     * Set fechaModif
     *
     * @param \DateTime $fechaModif
     * @return Dependencias
     */
    public function setFechaModif($fechaModif)
    {
        $this->fechaModif = $fechaModif;

        return $this;
    }

    /**
     * Get fechaModif
     *
     * @return \DateTime 
     */
    public function getFechaModif()
    {
        return $this->fechaModif;
    }

    /**
     * Set usuModif
     *
     * @param string $usuModif
     * @return Dependencias
     */
    public function setUsuModif($usuModif)
    {
        $this->usuModif = $usuModif;

        return $this;
    }

    /**
     * Get usuModif
     *
     * @return string 
     */
    public function getUsuModif()
    {
        return $this->usuModif;
    }

    /**
     * Set codpost
     *
     * @param string $codpost
     * @return Dependencias
     */
    public function setCodpost($codpost)
    {
        $this->codpost = $codpost;

        return $this;
    }

    /**
     * Get codpost
     *
     * @return string 
     */
    public function getCodpost()
    {
        return $this->codpost;
    }

    /**
     * Set auxTipoJurisdiccion
     *
     * @param string $auxTipoJurisdiccion
     * @return Dependencias
     */
    public function setAuxTipoJurisdiccion($auxTipoJurisdiccion)
    {
        $this->auxTipoJurisdiccion = $auxTipoJurisdiccion;

        return $this;
    }

    /**
     * Get auxTipoJurisdiccion
     *
     * @return string 
     */
    public function getAuxTipoJurisdiccion()
    {
        return $this->auxTipoJurisdiccion;
    }

    /**
     * Set latitud
     *
     * @param integer $latitud
     * @return Dependencias
     */
    public function setLatitud($latitud)
    {
        $this->latitud = $latitud;

        return $this;
    }

    /**
     * Get latitud
     *
     * @return integer 
     */
    public function getLatitud()
    {
        return $this->latitud;
    }

    /**
     * Set longitud
     *
     * @param integer $longitud
     * @return Dependencias
     */
    public function setLongitud($longitud)
    {
        $this->longitud = $longitud;

        return $this;
    }

    /**
     * Get longitud
     *
     * @return integer 
     */
    public function getLongitud()
    {
        return $this->longitud;
    }
    
    
    
    /**
     * @ORM\ManyToOne(targetEntity="TipoDependencia")
     * @ORM\JoinColumn(name="Tipo_dependencia", referencedColumnName="ID_TIPO_DEPENDENCIA", nullable=false)
     * @GRID\Column(field="tipodependencia.codTipoDependencia", type="text", title="TipoDependencia", defaultOperator="eq", operatorsVisible=false, operators={"eq"})
     */
    protected $tipodependencia;
    
    public function __construct() {
        $this->tipodependencia = new ArrayCollection();
    }
    
    
    /**
     * Set tipodependencia
     *
     * @param TipoDependencia $tipodependencia
     * @return TipoDependencia
     */
    public function setTipoDependencia(TipoDependencia $tipodependencia)
    {
        $this->tipodependencia = $tipodependencia;

        return $this;
    }

    /**
     * Get tipodependencia
     *
     * @return TipoDependencia 
     */
    public function getTipoDependencia()
    {
        return $this->tipodependencia;
    }
    
    
    public function __toString()
    {
        return $this->getCodigo()." - ".$this->getNombre();
    }
}
