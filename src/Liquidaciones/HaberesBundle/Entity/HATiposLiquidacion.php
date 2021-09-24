<?php


namespace Liquidaciones\HaberesBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * HATiposLiquidacion
 *
 * @ORM\Table("haberes.Haberes.HATiposLiquidacion")
 * @ORM\Entity(repositoryClass="Liquidaciones\HaberesBundle\Entity\HATiposLiquidacionRepository")
 */

class HATiposLiquidacion 
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IdTipoLiquidacion", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="TipoLiquidacion", type="string")
     */
    private $tipoLiquidacion;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="EsGanancias", type="string")
     */
    private $esGanancias;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="IdTipoLiquidacionGcias", type="integer")
     */
    private $idTipoLiquidacionGcias;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="Prioridad", type="integer")
     */
    private $prioridad;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="Principal", type="string")
     */
    private $principal;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="SubPrincipal", type="string")
     */
    private $subPrincipal;
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="Vigente", type="string")
     */
    private $vigente;
    
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="EsAdministradoPorHaberes", type="string")
     */
    private $esAdministradoPorHaberes;
    
    
    
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
     * @ORM\ManyToOne(targetEntity="HACabecera", inversedBy="HATiposLiquidacion")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefTipoLiquidacion")
     */
    
    /**
     * Set tipoLiquidacion
     *
     * @param string $tipoLiquidacion
     * @return HATiposLiquidacion
     */
    public function setTipoLiquidacion($tipoLiquidacion)
    {
        $this->tipoLiquidacion = $tipoLiquidacion;

        return $this;
    }

    /**
     * Get tipoLiquidacion
     *
     * @return string 
     */
    public function getTipoLiquidacion()
    {
        return $this->tipoLiquidacion;
    }
    
    
    
    /**
     * Set esGanancia
     *
     * @param string $esGanancia
     * @return HATiposLiquidacion
     */
    public function setEsGanancia($esGanancia)
    {
        $this->esGanancia = $esGanancia;

        return $this;
    }

    /**
     * Get esGanancia
     *
     * @return string 
     */
    public function getEsGanancia()
    {
        return $this->esGanancia;
    }
    
    
    
    /**
     * Set idTipoLiquidacionGcias
     *
     * @param integer $idTipoLiquidacionGcias
     * @return HATiposLiquidacion
     */
    public function setIdTipoLiquidacionGcias($idTipoLiquidacionGcias)
    {
        $this->idTipoLiquidacionGcias = $idTipoLiquidacionGcias;

        return $this;
    }

    /**
     * Get idTipoLiquidacionGcias
     *
     * @return integer 
     */
    public function getIdTipoLiquidacionGcias()
    {
        return $this->idTipoLiquidacionGcias;
    }
    
    
    
    /**
     * Set prioridad
     *
     * @param integer $prioridad
     * @return HATiposLiquidacion
     */
    public function setPrioridad($prioridad)
    {
        $this->prioridad = $prioridad;

        return $this;
    }

    /**
     * Get prioridad
     *
     * @return integer 
     */
    public function getPrioridad()
    {
        return $this->prioridad;
    }
    
    
    
    
    /**
     * Set principal
     *
     * @param string $principal
     * @return HATiposLiquidacion
     */
    public function setPrincipal($principal)
    {
        $this->prioridad = $principal;

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
     * @return HATiposLiquidacion
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
     * Set vigente
     *
     * @param string $vigente
     * @return HATiposLiquidacion
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
    
    
    
    
    /**
     * Set esAdministradaPorHaberes
     *
     * @param string $esAdministradaPorHaberes
     * @return HATiposLiquidacion
     */
    public function setEsAdministradaPorHaberes($esAdministradaPorHaberes)
    {
        $this->esAdministradaPorHaberes = $esAdministradaPorHaberes;

        return $this;
    }

    /**
     * Get esAdministradaPorHaberes
     *
     * @return string 
     */
    public function getEsAdministradaPorHaberes()
    {
        return $this->esAdministradaPorHaberes;
    }
    
    public function __toString()
    {
        return $this->tipoLiquidacion();
    }
}
