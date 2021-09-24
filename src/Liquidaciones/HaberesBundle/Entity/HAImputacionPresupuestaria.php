<?php


namespace Liquidaciones\HaberesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;


/**
 * HAImputacionPresupuestaria
 *
 * @ORM\Table("haberes.Haberes.HAImputacionPresupuestaria")
 * @ORM\Entity(repositoryClass="Liquidaciones\HaberesBundle\Entity\HAImputacionPresupuestariaRepository")
 */
class HAImputacionPresupuestaria {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="IdImputacionPresupuestaria", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdJurisdiccionPresupuestaria", type="integer")
     */
    private $idJurisdiccionPresupuestaria;
    
    /**
     * @var string
     *
     * @ORM\Column(name="DependenciaPresupuestaria", type="string", length=4)
     */
    private $dependenciaPresupuestaria;
    
    /**
     * @var string
     *
     * @ORM\Column(name="PrincipalSubPrinc", type="string", length=2)
     */
    private $principalSubPrinc;
    
    /**
     * @var string
     *
     * @ORM\Column(name="RegimenEstatutario", type="string", length=2)
     */
    private $regimenEstatutario;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Agrupamiento", type="string", length=2)
     */
    private $agrupamiento;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Descripcion", type="string", length=50)
     */
    private $descripcion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ProgramaDescripcion", type="string", length=18)
     */
    private $programaDescripcion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Ace", type="string", length=4)
     */
    private $ace;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Aco", type="string", length=4)
     */
    private $aco;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Pan", type="string", length=4)
     */
    private $pan;
    
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
     * @ORM\Column(name="Principal", type="string", length=1)
     */
    private $principal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="SubPrincipal", type="string", length=1)
     */
    private $subPrincipal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Parcial", type="string", length=1)
     */
    private $parcial;
    
    /**
     * @var string
     *
     * @ORM\Column(name="SubParcial", type="string", length=3)
     */
    private $subParcial;
    
    /**
     * @var string
     *
     * @ORM\Column(name="Activa", type="string", length=1)
     */
    private $activa;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="Cargos", type="integer")
     */
    private $cargos;
    
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="Credito", type="decimal",precision=18, scale=2)
     */
    private $credito;
    
    
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
     * Set idJurisdiccionPresupuestaria
     *
     * @param integer $idJurisdiccionPresupuestaria
     * @return HAImputacionPresupuestaria
     */
    public function setIdJurisdiccionPresupuestaria($idJurisdiccionPresupuestaria)
    {
        $this->idJurisdiccionPresupuestaria = $idJurisdiccionPresupuestaria;

        return $this;
    }

    /**
     * Get idJurisdiccionPresupuestaria
     *
     * @return integer 
     */
    public function getIdJurisdiccionPresupuestaria()
    {
        return $this->idJurisdiccionPresupuestaria;
    }
    
    
    
    /**
     * Set dependenciaPresupuestaria
     *
     * @param string $dependenciaPresupuestaria
     * @return HAImputacionPresupuestaria
     */
    public function setDependenciaPresupuestaria($dependenciaPresupuestaria)
    {
        $this->dependenciaPresupuestaria = $dependenciaPresupuestaria;

        return $this;
    }

    /**
     * Get dependenciaPresupuestaria
     *
     * @return string 
     */
    public function getDependenciaPresupuestaria()
    {
        return $this->dependenciaPresupuestaria;
    }
    
    
    
    /**
     * Set principalSubPrinc
     *
     * @param string $principalSubPrinc
     * @return HAImputacionPresupuestaria
     */
    public function setPrincipalSubPrinc($principalSubPrinc)
    {
        $this->principalSubPrinc = $principalSubPrinc;

        return $this;
    }

    /**
     * Get principalSubPrinc
     *
     * @return string 
     */
    public function getPrincipalSubPrinc()
    {
        return $this->principalSubPrinc;
    }
    
    
    
    /**
     * Set regimenEstatutario
     *
     * @param string $regimenEstatutario
     * @return HAImputacionPresupuestaria
     */
    public function setRegimenEstatutario($regimenEstatutario)
    {
        $this->regimenEstatutario = $regimenEstatutario;

        return $this;
    }

    /**
     * Get regimenEstatutario
     *
     * @return string 
     */
    public function getRegimenEstatutario()
    {
        return $this->regimenEstatutario;
    }
    
    
    /**
     * Set agrupamiento
     *
     * @param string $agrupamiento
     * @return HAImputacionPresupuestaria
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return HAImputacionPresupuestaria
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = utf8_decode($descripcion);

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return utf8_encode($this->descripcion);
    }
    
    
    /**
     * Set programaDescripcion
     *
     * @param string $programaDescripcion
     * @return HAImputacionPresupuestaria
     */
    public function setProgramaDescripcion($programaDescripcion)
    {
        $this->programaDescripcion = utf8_decode($programaDescripcion);

        return $this;
    }

    /**
     * Get programaDescripcion
     *
     * @return string 
     */
    public function getProgramaDescripcion()
    {
        return utf8_encode($this->programaDescripcion);
    }
    
    
    
    /**
     * Set ace
     *
     * @param string $ace
     * @return HAImputacionPresupuestaria
     */
    public function setAce($ace)
    {
        $this->ace = $ace;

        return $this;
    }

    /**
     * Get ace
     *
     * @return string 
     */
    public function getAce()
    {
        return $this->ace;
    }
    
    
    /**
     * Set aco
     *
     * @param string $aco
     * @return HAImputacionPresupuestaria
     */
    public function setAco($aco)
    {
        $this->aco = $aco;

        return $this;
    }

    /**
     * Get aco
     *
     * @return string 
     */
    public function getAco()
    {
        return $this->aco;
    }
    
    
    
    /**
     * Set pan
     *
     * @param string $pan
     * @return HAImputacionPresupuestaria
     */
    public function setPan($pan)
    {
        $this->pan = $pan;

        return $this;
    }

    /**
     * Get pan
     *
     * @return string 
     */
    public function getPan()
    {
        return $this->pan;
    }
    
    
    /**
     * Set programa
     *
     * @param string $programa
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * @return HAImputacionPresupuestaria
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
     * Set principal
     *
     * @param string $principal
     * @return HAImputacionPresupuestaria
     */
    public function setPrincipal($principal)
    {
        $this->principal = $principal;

        return $this;
    }

    
    /**
     * Get principal
     *
     * @return string 
     */
    public function getPrincipal()
    {
        return $this->principal;
    }
    
    
    
    /**
     * Set subPrincipal
     *
     * @param string $subPrincipal
     * @return HAImputacionPresupuestaria
     */
    public function setSubPrincipal($subPrincipal)
    {
        $this->subPrincipal = $subPrincipal;

        return $this;
    }

    
    /**
     * Get subPrincipal
     *
     * @return string 
     */
    public function getSubPrincipal()
    {
        return $this->subPrincipal;
    }
    
    
    
    /**
     * Set parcial
     *
     * @param string $parcial
     * @return HAImputacionPresupuestaria
     */
    public function setParcial($parcial)
    {
        $this->parcial = $parcial;

        return $this;
    }

    
    /**
     * Get parcial
     *
     * @return string 
     */
    public function getParcial()
    {
        return $this->parcial;
    }
    
    
    
    /**
     * Set subParcial
     *
     * @param string $subParcial
     * @return HAImputacionPresupuestaria
     */
    public function setSubParcial($subParcial)
    {
        $this->subParcial = $subParcial;

        return $this;
    }

    
    /**
     * Get subParcial
     *
     * @return string 
     */
    public function getSubParcial()
    {
        return $this->subParcial;
    }
    
    
    
    /**
     * Set activa
     *
     * @param string $activa
     * @return HAImputacionPresupuestaria
     */
    public function setActiva($activa)
    {
        $this->activa = $activa;

        return $this;
    }

    
    /**
     * Get activa
     *
     * @return string 
     */
    public function getActiva()
    {
        return $this->activa;
    }
    
    
    
    /**
     * Set cargos
     *
     * @param string $cargos
     * @return HAImputacionPresupuestaria
     */
    public function setCargos($cargos)
    {
        $this->cargos = $cargos;

        return $this;
    }

    
    /**
     * Get cargos
     *
     * @return string 
     */
    public function getCargos()
    {
        return $this->cargos;
    }
    
    
    
    /**
     * Set credito
     *
     * @param string $credito
     * @return HAImputacionPresupuestaria
     */
    public function setCredito($credito)
    {
        $this->credito = $credito;

        return $this;
    }

    
    /**
     * Get credito
     *
     * @return string 
     */
    public function getCredito()
    {
        return $this->credito;
    }

}
