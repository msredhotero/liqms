<?php

namespace Liquidaciones\ReferenciasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Imppresupff11
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Liquidaciones\ReferenciasBundle\Entity\Imppresupff11Repository")
 */
class Imppresupff11
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
     * @var string
     *
     * @ORM\Column(name="Jurisdiccion", type="string", length=2)
     */
    private $jurisdiccion;

    /**
     * @var string
     *
     * @ORM\Column(name="JuridisdiccionAuxiliar", type="string", length=2)
     */
    private $juridisdiccionAuxiliar;

    /**
     * @var string
     *
     * @ORM\Column(name="Desc_entidad", type="string", length=80)
     */
    private $descEntidad;

    /**
     * @var string
     *
     * @ORM\Column(name="Cod_jurisdiccion", type="string", length=3)
     */
    private $codJurisdiccion;

    /**
     * @var string
     *
     * @ORM\Column(name="Regimenestatutario", type="string", length=2)
     */
    private $regimenestatutario;

    /**
     * @var string
     *
     * @ORM\Column(name="Agrupamiento", type="string", length=2)
     */
    private $agrupamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="ProgramaDescripcion", type="string", length=18)
     */
    private $programaDescripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="ACE", type="string", length=4)
     */
    private $aCE;

    /**
     * @var string
     *
     * @ORM\Column(name="ACO", type="string", length=4)
     */
    private $aCO;

    /**
     * @var string
     *
     * @ORM\Column(name="PAN", type="string", length=4)
     */
    private $pAN;

    /**
     * @var string
     *
     * @ORM\Column(name="Programa", type="string", length=4)
     */
    private $programa;

    /**
     * @var string
     *
     * @ORM\Column(name="SubPrograma", type="string", length=4)
     */
    private $subPrograma;

    /**
     * @var string
     *
     * @ORM\Column(name="Proyecto", type="string", length=4)
     */
    private $proyecto;

    /**
     * @var string
     *
     * @ORM\Column(name="Grupo", type="string", length=4)
     */
    private $grupo;

    /**
     * @var string
     *
     * @ORM\Column(name="AES", type="string", length=4)
     */
    private $aES;

    /**
     * @var string
     *
     * @ORM\Column(name="SubGrupo", type="string", length=4)
     */
    private $subGrupo;

    /**
     * @var string
     *
     * @ORM\Column(name="Obra", type="string", length=4)
     */
    private $obra;

    /**
     * @var string
     *
     * @ORM\Column(name="UbicacionGeografica", type="string", length=4)
     */
    private $ubicacionGeografica;

    /**
     * @var string
     *
     * @ORM\Column(name="Finalidad", type="string", length=1)
     */
    private $finalidad;

    /**
     * @var string
     *
     * @ORM\Column(name="Funcion", type="string", length=1)
     */
    private $funcion;

    /**
     * @var string
     *
     * @ORM\Column(name="SubFuncion", type="string", length=1)
     */
    private $subFuncion;

    /**
     * @var string
     *
     * @ORM\Column(name="Procedencia", type="string", length=1)
     */
    private $procedencia;

    /**
     * @var string
     *
     * @ORM\Column(name="Fuente", type="string", length=1)
     */
    private $fuente;

    /**
     * @var string
     *
     * @ORM\Column(name="Partida_ppal", type="string", length=1)
     */
    private $partidaPpal;

    /**
     * @var string
     *
     * @ORM\Column(name="Partida_subppal", type="string", length=1)
     */
    private $partidaSubppal;

    /**
     * @var string
     *
     * @ORM\Column(name="Partida_parcial", type="string", length=1)
     */
    private $partidaParcial;

    /**
     * @var string
     *
     * @ORM\Column(name="Partida_subparcial", type="string", length=3)
     */
    private $partidaSubparcial;

    /**
     * @var string
     *
     * @ORM\Column(name="Entidad", type="string", length=3)
     */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="CodEstable", type="string", length=4)
     */
    private $codEstable;

    /**
     * @var integer
     *
     * @ORM\Column(name="Origen_fondos", type="integer")
     */
    private $origenFondos;

    /**
     * @var string
     *
     * @ORM\Column(name="CodInstitucional", type="string", length=6)
     */
    private $codInstitucional;

    /**
     * @var string
     *
     * @ORM\Column(name="Desc_imputacionpresup", type="string", length=100)
     */
    private $descImputacionpresup;


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
     * Set jurisdiccion
     *
     * @param string $jurisdiccion
     * @return Imppresupff11
     */
    public function setJurisdiccion($jurisdiccion)
    {
        $this->jurisdiccion = $jurisdiccion;

        return $this;
    }

    /**
     * Get jurisdiccion
     *
     * @return string 
     */
    public function getJurisdiccion()
    {
        return $this->jurisdiccion;
    }

    /**
     * Set juridisdiccionAuxiliar
     *
     * @param string $juridisdiccionAuxiliar
     * @return Imppresupff11
     */
    public function setJuridisdiccionAuxiliar($juridisdiccionAuxiliar)
    {
        $this->juridisdiccionAuxiliar = $juridisdiccionAuxiliar;

        return $this;
    }

    /**
     * Get juridisdiccionAuxiliar
     *
     * @return string 
     */
    public function getJuridisdiccionAuxiliar()
    {
        return $this->juridisdiccionAuxiliar;
    }

    /**
     * Set descEntidad
     *
     * @param string $descEntidad
     * @return Imppresupff11
     */
    public function setDescEntidad($descEntidad)
    {
        $this->descEntidad = $descEntidad;

        return $this;
    }

    /**
     * Get descEntidad
     *
     * @return string 
     */
    public function getDescEntidad()
    {
        return $this->descEntidad;
    }

    /**
     * Set codJurisdiccion
     *
     * @param string $codJurisdiccion
     * @return Imppresupff11
     */
    public function setCodJurisdiccion($codJurisdiccion)
    {
        $this->codJurisdiccion = $codJurisdiccion;

        return $this;
    }

    /**
     * Get codJurisdiccion
     *
     * @return string 
     */
    public function getCodJurisdiccion()
    {
        return $this->codJurisdiccion;
    }

    /**
     * Set regimenestatutario
     *
     * @param string $regimenestatutario
     * @return Imppresupff11
     */
    public function setRegimenestatutario($regimenestatutario)
    {
        $this->regimenestatutario = $regimenestatutario;

        return $this;
    }

    /**
     * Get regimenestatutario
     *
     * @return string 
     */
    public function getRegimenestatutario()
    {
        return $this->regimenestatutario;
    }

    /**
     * Set agrupamiento
     *
     * @param string $agrupamiento
     * @return Imppresupff11
     */
    public function setAgrupamiento($agrupamiento)
    {
        $this->agrupamiento = $agrupamiento;

        return $this;
    }

    /**
     * Get agrupamiento
     *
     * @return string 
     */
    public function getAgrupamiento()
    {
        return $this->agrupamiento;
    }

    /**
     * Set programaDescripcion
     *
     * @param string $programaDescripcion
     * @return Imppresupff11
     */
    public function setProgramaDescripcion($programaDescripcion)
    {
        $this->programaDescripcion = $programaDescripcion;

        return $this;
    }

    /**
     * Get programaDescripcion
     *
     * @return string 
     */
    public function getProgramaDescripcion()
    {
        return $this->programaDescripcion;
    }

    /**
     * Set aCE
     *
     * @param string $aCE
     * @return Imppresupff11
     */
    public function setACE($aCE)
    {
        $this->aCE = $aCE;

        return $this;
    }

    /**
     * Get aCE
     *
     * @return string 
     */
    public function getACE()
    {
        return $this->aCE;
    }

    /**
     * Set aCO
     *
     * @param string $aCO
     * @return Imppresupff11
     */
    public function setACO($aCO)
    {
        $this->aCO = $aCO;

        return $this;
    }

    /**
     * Get aCO
     *
     * @return string 
     */
    public function getACO()
    {
        return $this->aCO;
    }

    /**
     * Set pAN
     *
     * @param string $pAN
     * @return Imppresupff11
     */
    public function setPAN($pAN)
    {
        $this->pAN = $pAN;

        return $this;
    }

    /**
     * Get pAN
     *
     * @return string 
     */
    public function getPAN()
    {
        return $this->pAN;
    }

    /**
     * Set programa
     *
     * @param string $programa
     * @return Imppresupff11
     */
    public function setPrograma($programa)
    {
        $this->programa = $programa;

        return $this;
    }

    /**
     * Get programa
     *
     * @return string 
     */
    public function getPrograma()
    {
        return $this->programa;
    }

    /**
     * Set subPrograma
     *
     * @param string $subPrograma
     * @return Imppresupff11
     */
    public function setSubPrograma($subPrograma)
    {
        $this->subPrograma = $subPrograma;

        return $this;
    }

    /**
     * Get subPrograma
     *
     * @return string 
     */
    public function getSubPrograma()
    {
        return $this->subPrograma;
    }

    /**
     * Set proyecto
     *
     * @param string $proyecto
     * @return Imppresupff11
     */
    public function setProyecto($proyecto)
    {
        $this->proyecto = $proyecto;

        return $this;
    }

    /**
     * Get proyecto
     *
     * @return string 
     */
    public function getProyecto()
    {
        return $this->proyecto;
    }

    /**
     * Set grupo
     *
     * @param string $grupo
     * @return Imppresupff11
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return string 
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set aES
     *
     * @param string $aES
     * @return Imppresupff11
     */
    public function setAES($aES)
    {
        $this->aES = $aES;

        return $this;
    }

    /**
     * Get aES
     *
     * @return string 
     */
    public function getAES()
    {
        return $this->aES;
    }

    /**
     * Set subGrupo
     *
     * @param string $subGrupo
     * @return Imppresupff11
     */
    public function setSubGrupo($subGrupo)
    {
        $this->subGrupo = $subGrupo;

        return $this;
    }

    /**
     * Get subGrupo
     *
     * @return string 
     */
    public function getSubGrupo()
    {
        return $this->subGrupo;
    }

    /**
     * Set obra
     *
     * @param string $obra
     * @return Imppresupff11
     */
    public function setObra($obra)
    {
        $this->obra = $obra;

        return $this;
    }

    /**
     * Get obra
     *
     * @return string 
     */
    public function getObra()
    {
        return $this->obra;
    }

    /**
     * Set ubicacionGeografica
     *
     * @param string $ubicacionGeografica
     * @return Imppresupff11
     */
    public function setUbicacionGeografica($ubicacionGeografica)
    {
        $this->ubicacionGeografica = $ubicacionGeografica;

        return $this;
    }

    /**
     * Get ubicacionGeografica
     *
     * @return string 
     */
    public function getUbicacionGeografica()
    {
        return $this->ubicacionGeografica;
    }

    /**
     * Set finalidad
     *
     * @param string $finalidad
     * @return Imppresupff11
     */
    public function setFinalidad($finalidad)
    {
        $this->finalidad = $finalidad;

        return $this;
    }

    /**
     * Get finalidad
     *
     * @return string 
     */
    public function getFinalidad()
    {
        return $this->finalidad;
    }

    /**
     * Set funcion
     *
     * @param string $funcion
     * @return Imppresupff11
     */
    public function setFuncion($funcion)
    {
        $this->funcion = $funcion;

        return $this;
    }

    /**
     * Get funcion
     *
     * @return string 
     */
    public function getFuncion()
    {
        return $this->funcion;
    }

    /**
     * Set subFuncion
     *
     * @param string $subFuncion
     * @return Imppresupff11
     */
    public function setSubFuncion($subFuncion)
    {
        $this->subFuncion = $subFuncion;

        return $this;
    }

    /**
     * Get subFuncion
     *
     * @return string 
     */
    public function getSubFuncion()
    {
        return $this->subFuncion;
    }

    /**
     * Set procedencia
     *
     * @param string $procedencia
     * @return Imppresupff11
     */
    public function setProcedencia($procedencia)
    {
        $this->procedencia = $procedencia;

        return $this;
    }

    /**
     * Get procedencia
     *
     * @return string 
     */
    public function getProcedencia()
    {
        return $this->procedencia;
    }

    /**
     * Set fuente
     *
     * @param string $fuente
     * @return Imppresupff11
     */
    public function setFuente($fuente)
    {
        $this->fuente = $fuente;

        return $this;
    }

    /**
     * Get fuente
     *
     * @return string 
     */
    public function getFuente()
    {
        return $this->fuente;
    }

    /**
     * Set partidaPpal
     *
     * @param string $partidaPpal
     * @return Imppresupff11
     */
    public function setPartidaPpal($partidaPpal)
    {
        $this->partidaPpal = $partidaPpal;

        return $this;
    }

    /**
     * Get partidaPpal
     *
     * @return string 
     */
    public function getPartidaPpal()
    {
        return $this->partidaPpal;
    }

    /**
     * Set partidaSubppal
     *
     * @param string $partidaSubppal
     * @return Imppresupff11
     */
    public function setPartidaSubppal($partidaSubppal)
    {
        $this->partidaSubppal = $partidaSubppal;

        return $this;
    }

    /**
     * Get partidaSubppal
     *
     * @return string 
     */
    public function getPartidaSubppal()
    {
        return $this->partidaSubppal;
    }

    /**
     * Set partidaParcial
     *
     * @param string $partidaParcial
     * @return Imppresupff11
     */
    public function setPartidaParcial($partidaParcial)
    {
        $this->partidaParcial = $partidaParcial;

        return $this;
    }

    /**
     * Get partidaParcial
     *
     * @return string 
     */
    public function getPartidaParcial()
    {
        return $this->partidaParcial;
    }

    /**
     * Set partidaSubparcial
     *
     * @param string $partidaSubparcial
     * @return Imppresupff11
     */
    public function setPartidaSubparcial($partidaSubparcial)
    {
        $this->partidaSubparcial = $partidaSubparcial;

        return $this;
    }

    /**
     * Get partidaSubparcial
     *
     * @return string 
     */
    public function getPartidaSubparcial()
    {
        return $this->partidaSubparcial;
    }

    /**
     * Set entidad
     *
     * @param string $entidad
     * @return Imppresupff11
     */
    public function setEntidad($entidad)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad
     *
     * @return string 
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set codEstable
     *
     * @param string $codEstable
     * @return Imppresupff11
     */
    public function setCodEstable($codEstable)
    {
        $this->codEstable = $codEstable;

        return $this;
    }

    /**
     * Get codEstable
     *
     * @return string 
     */
    public function getCodEstable()
    {
        return $this->codEstable;
    }

    /**
     * Set origenFondos
     *
     * @param integer $origenFondos
     * @return Imppresupff11
     */
    public function setOrigenFondos($origenFondos)
    {
        $this->origenFondos = $origenFondos;

        return $this;
    }

    /**
     * Get origenFondos
     *
     * @return integer 
     */
    public function getOrigenFondos()
    {
        return $this->origenFondos;
    }

    /**
     * Set codInstitucional
     *
     * @param string $codInstitucional
     * @return Imppresupff11
     */
    public function setCodInstitucional($codInstitucional)
    {
        $this->codInstitucional = $codInstitucional;

        return $this;
    }

    /**
     * Get codInstitucional
     *
     * @return string 
     */
    public function getCodInstitucional()
    {
        return $this->codInstitucional;
    }

    /**
     * Set descImputacionpresup
     *
     * @param string $descImputacionpresup
     * @return Imppresupff11
     */
    public function setDescImputacionpresup($descImputacionpresup)
    {
        $this->descImputacionpresup = $descImputacionpresup;

        return $this;
    }

    /**
     * Get descImputacionpresup
     *
     * @return string 
     */
    public function getDescImputacionpresup()
    {
        return $this->descImputacionpresup;
    }
}
