<?php

namespace Liquidaciones\ReferenciasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImputacionDependencias
 *
 * @ORM\Table(name="trefe14.imputacion_dependencias")
 * @ORM\Entity(repositoryClass="Liquidaciones\ReferenciasBundle\Entity\ImputacionDependenciasRepository")
 */
class ImputacionDependencias
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IDIMPUTACIONDEPEPAGO", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="CodigoDependencia", type="string", length=4)
     */
    private $codigoDependencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="Id_ImputacionPresupuestaria", type="integer")
     */
    private $idImputacionPresupuestaria;

    /**
     * @var string
     *
     * @ORM\Column(name="UsuaModi", type="string", length=8)
     */
    private $usuaModi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaModi", type="date")
     */
    private $fechaModi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Fvigencia_desde", type="date")
     */
    private $fvigenciaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Fvigencia_hasta", type="date")
     */
    private $fvigenciaHasta;


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
     * Set codigoDependencia
     *
     * @param string $codigoDependencia
     * @return ImputacionDependencias
     */
    public function setCodigoDependencia($codigoDependencia)
    {
        $this->codigoDependencia = $codigoDependencia;

        return $this;
    }

    /**
     * Get codigoDependencia
     *
     * @return string 
     */
    public function getCodigoDependencia()
    {
        return $this->codigoDependencia;
    }

    /**
     * Set idImputacionPresupuestaria
     *
     * @param integer $idImputacionPresupuestaria
     * @return ImputacionDependencias
     */
    public function setIdImputacionPresupuestaria($idImputacionPresupuestaria)
    {
        $this->idImputacionPresupuestaria = $idImputacionPresupuestaria;

        return $this;
    }

    /**
     * Get idImputacionPresupuestaria
     *
     * @return integer 
     */
    public function getIdImputacionPresupuestaria()
    {
        return $this->idImputacionPresupuestaria;
    }

    /**
     * Set usuaModi
     *
     * @param string $usuaModi
     * @return ImputacionDependencias
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
     * @return ImputacionDependencias
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
     * Set fvigenciaDesde
     *
     * @param \DateTime $fvigenciaDesde
     * @return ImputacionDependencias
     */
    public function setFvigenciaDesde($fvigenciaDesde)
    {
        $this->fvigenciaDesde = $fvigenciaDesde;

        return $this;
    }

    /**
     * Get fvigenciaDesde
     *
     * @return \DateTime 
     */
    public function getFvigenciaDesde()
    {
        return $this->fvigenciaDesde;
    }

    /**
     * Set fvigenciaHasta
     *
     * @param \DateTime $fvigenciaHasta
     * @return ImputacionDependencias
     */
    public function setFvigenciaHasta($fvigenciaHasta)
    {
        $this->fvigenciaHasta = $fvigenciaHasta;

        return $this;
    }

    /**
     * Get fvigenciaHasta
     *
     * @return \DateTime 
     */
    public function getFvigenciaHasta()
    {
        return $this->fvigenciaHasta;
    }
}
