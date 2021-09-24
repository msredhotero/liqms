<?php

namespace Liquidaciones\ReferenciasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * TipoDependencia
 *
 * @ORM\Table(name="trefe14.tipo_dependencia")
 * @ORM\Entity(repositoryClass="Liquidaciones\ReferenciasBundle\Entity\TipoDependenciaRepository")
 */
class TipoDependencia
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID_TIPO_DEPENDENCIA", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Cod_Tipo_Dependencia", type="string", length=5)
     * @GRID\Column(title="codTipoDependencia", visible=true, operators={"like"},defaultOperator="like", operatorsVisible=false)
     */
    private $codTipoDependencia;

    /**
     * @var string
     *
     * @ORM\Column(name="Desc_Tipo_Dependencia", type="string", length=255)
     * @GRID\Column(title="descTipoDependencia", visible=true, operators={"like"},defaultOperator="like", operatorsVisible=false)
     */
    private $descTipoDependencia;


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
     * @ORM\ManyToOne(targetEntity="Depedencias", inversedBy="TipoDependencia")
     * @ORM\JoinColumn(name="ID_TIPO_DEPENDENCIA", referencedColumnName="Tipo_dependencia")
     */
    
    /**
     * Set codTipoDependencia
     *
     * @param string $codTipoDependencia
     * @return TipoDependencia
     */
    public function setCodTipoDependencia($codTipoDependencia)
    {
        $this->codTipoDependencia = $codTipoDependencia;

        return $this;
    }

    /**
     * Get codTipoDependencia
     *
     * @return string 
     */
    public function getCodTipoDependencia()
    {
        return $this->codTipoDependencia;
    }

    /**
     * Set descTipoDependencia
     *
     * @param string $descTipoDependencia
     * @return TipoDependencia
     */
    public function setDescTipoDependencia($descTipoDependencia)
    {
        $this->descTipoDependencia = $descTipoDependencia;

        return $this;
    }

    /**
     * Get descTipoDependencia
     *
     * @return string 
     */
    public function getDescTipoDependencia()
    {
        return $this->descTipoDependencia;
    }
    
    
    public function __toString()
    {
        return $this->getCodTipoDependencia()." - ".$this->getDescTipoDependencia();
    }
}
