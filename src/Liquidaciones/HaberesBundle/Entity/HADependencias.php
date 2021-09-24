<?php

namespace Liquidaciones\HaberesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * HADependencias
 *
 * @ORM\Table(name="haberes.General.HADependencias")
 * @ORM\Entity(repositoryClass="Liquidaciones\HaberesBundle\Entity\HADependenciasRepository")
 */
class HADependencias
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IdDependencia", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Dependencia", type="string", length=4)
     */
    private $dependencia;

    /**
     * @var string
     *
     * @ORM\Column(name="activa", type="string", length=1)
     */
    private $habilitado;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="RegSan1", type="string", length=10)
     */
    private $regsan1;


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
     * Set dependencia
     *
     * @param string $dependencia
     * @return HADependencias
     */
    public function setDependencia($dependencia)
    {
        $this->dependencia = utf8_decode($dependencia);
    
        return $this;
    }

    /**
     * Get dependencia
     *
     * @return string 
     */
    public function getDependencia()
    {
        return utf8_encode($this->dependencia);
    }

    /**
     * Set habilitado
     *
     * @param string $habilitado
     * @return HADependencias
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
     * Set regsan1
     *
     * @param string $regsan1
     * @return HADependencias
     */
    public function setRegsan1($regsan1)
    {
        $this->regsan1 = $regsan1;
    
        return $this;
    }

    /**
     * Get regsan1
     *
     * @return string 
     */
    public function getRegsan1()
    {
        return $this->regsan1;
    }
    
    
    function __toString(){
        return  $this->getId().' - '.utf8_encode($this->getDependencia());
    }
}
