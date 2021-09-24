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
 * TipoModulos
 *
 * @ORM\Table("LiquidacionesWeb.dbo.TipoModulos")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuposRepository")
 */
class TipoModulos
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
     * @ORM\Column(name="Modulo", type="string", length=50, nullable=false)
     */
    private $modulo;

    /**
     * @var string
     *
     * @ORM\Column(name="Vigente", type="string", length=1, nullable=false)
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
     * @ORM\ManyToOne(targetEntity="ValorModulo", inversedBy="TipoModulos")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefTipoModulos")
     */
    
    /**
     * @ORM\ManyToOne(targetEntity="ValidacionModulos", inversedBy="TipoModulos")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefTipoModulos")
     */
    

    
 


    /**
     * Set modulo
     *
     * @param string $modulo
     * @return string
     */
    public function setModulo($modulo)
    {
        $this->modulo = utf8_decode($modulo);

        return $this;
    }

    /**
     * Get modulo
     *
     * @return string 
     */
    public function geModulo()
    {
        return utf8_encode($this->modulo);
    }


    /**
     * Set vigente
     *
     * @param string $vigente
     * @return string
     */
    public function setVigente($vigente)
    {
        $this->vigente = utf8_decode($vigente);

        return $this;
    }

    /**
     * Get vigente
     *
     * @return string 
     */
    public function geVigente()
    {
        return utf8_encode($this->vigente);
    }


   

    public function __toString()
    {
        return $this->getModulo();
    }


}
