<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Vacantes
 *
 * @ORM\Table("LiquidacionesWeb.dbo.vacantes")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\VacantesRepository")
 */
class Vacantes
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
     * @var integer
     *
     * @ORM\Column(name="RefCupo", type="integer")
     */
    private $refCupo;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="Vacantes", type="integer")
     */
    private $vacantes;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="UsuaCrea", type="string", length=20, nullable=true)
     */
    private $usuaCrea;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaCrea", type="datetime", nullable=true)
     */
    private $fechaCrea;

    /**
     * @var string
     *
     * @ORM\Column(name="UsuaModi", type="string", length=20, nullable=true)
     */
    private $usuaModi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaModi", type="datetime", nullable=true)
     */
    private $fechaModi;


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
     * Set refCupo
     *
     * @param integer $refCupo
     * @return Vacantes
     */
    public function setRefCupo($refCupo)
    {
        $this->refCupo = $refCupo;

        return $this;
    }

    /**
     * Get refCupo
     *
     * @return integer 
     */
    public function getRefCupo()
    {
        return $this->refCupo;
    }
    
    
    /**
     * Set vacantes
     *
     * @param integer $vacantes
     * @return Vacantes
     */
    public function setVacantes($vacantes)
    {
        $this->vacantes = $vacantes * 5;

        return $this;
    }

    /**
     * Get vacantes
     *
     * @return integer 
     */
    public function getVacantes()
    {
        return $this->vacantes;
    }
    
    
    /**
     * Set usuaCrea
     *
     * @param string $usuaCrea
     * @return Vacantes
     */
    public function setUsuaCrea($usuaCrea)
    {
        $this->usuaCrea = $usuaCrea;

        return $this;
    }

    /**
     * Get usuaCrea
     *
     * @return string 
     */
    public function getUsuaCrea()
    {
        return $this->usuaCrea;
    }

    /**
     * Set fechaCrea
     *
     * @param \DateTime $fechaCrea
     * @return Vacantes
     */
    public function setFechaCrea($fechaCrea)
    {
        $this->fechaCrea = $fechaCrea;

        return $this;
    }

    /**
     * Get fechaCrea
     *
     * @return \DateTime 
     */
    public function getFechaCrea()
    {
        return $this->fechaCrea;
    }
    
    

    /**
     * Set usuaModi
     *
     * @param string $usuaModi
     * @return Vacantes
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
     * Set fechaModi
     *
     * @param \DateTime $fechaModi
     * @return Vacantes
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
     * @ORM\ManyToOne(targetEntity="Cupos")
     * @ORM\JoinColumn(name="RefCupo", referencedColumnName="id", nullable=false)
     */
    protected $cupos;

    public function __construct() {

        $this->cupos = new ArrayCollection();
    }
    
    
    /**
     * Set cupos
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\Cupos $cupos
     * @return Vacantes
     */
    public function setCupos(\Liquidaciones\CuposAnualesBundle\Entity\Cupos $cupos=null)
    {
        $this->cupos = $cupos;

        return $this;
    }

    /**
     * Get cupos
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\Cupos 
     */
    public function getCupos()
    {
        return $this->cupos;
    }

    

}
