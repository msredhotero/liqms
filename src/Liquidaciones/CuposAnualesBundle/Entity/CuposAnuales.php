<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuposAnuales
 *
 * @ORM\Table("LiquidacionesWeb.dbo.cuposanuales")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuposAnualesRepository")
 * 
 * @GRID\Source(columns="id, descripcion, anio, monto, activo")
 * 
 */
class CuposAnuales
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
     * @ORM\Column(name="Descripcion", type="string", length=100)
     * @Assert\NotBlank()
     * @GRID\Column(title="Descripción", visible=true,  operatorsVisible=true)
     */
    private $descripcion;

    /**
     * @var integer
     *
     * @ORM\Column(name="Anio", type="integer")
     * @GRID\Column(title="Año", visible=true, operators={"eq"},defaultOperator="eq",operatorsVisible=false)
     */
    private $anio;

    /**
     * @var string
     *
     * @ORM\Column(name="Monto", type="decimal",precision=12, scale=2)
     * @Assert\NotBlank()
     * @Assert\Range(
     *      min = "1000",
     *      max = "99999999999",
     *      minMessage = "El monto debe ser mayor a 1000",
     *      maxMessage = "El presupuesto no puede superar los 100000000000",
     *      invalidMessage = "El valor '{{ value }}' no es un Número. Recuerde que para las decimas hay que ingresar ' . ' (punto)"
     * )
     * @GRID\Column(title="Monto", visible=true, filterable=false, type="number", style="decimal", precision=2)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="Activo", type="boolean", length=1)
     * @GRID\Column(title="Activo", visible=true, operators={"eq"},defaultOperator="eq",type="boolean", operatorsVisible=true,values={true="Si",false="No"} )
     */
    private $activo;

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
     * @ORM\ManyToOne(targetEntity="Cupos", inversedBy="CuposAnuales")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCupoAnual")
     */
    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return CuposAnuales
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
     * Set anio
     *
     * @param integer $anio
     * @return CuposAnuales
     */
    public function setAnio($anio)
    {
        $this->anio = $anio;

        return $this;
    }

    /**
     * Get anio
     *
     * @return integer 
     */
    public function getAnio()
    {
        return $this->anio;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return CuposAnuales
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return bool 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return CuposAnuales
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Set usuaCrea
     *
     * @param string $usuaCrea
     * @return CuposAnuales
     */
    public function setUsuaCrea($usuaCrea)
    {
        $this->usuaCrea = utf8_decode($usuaCrea);

        return $this;
    }

    /**
     * Get usuaCrea
     *
     * @return string 
     */
    public function getUsuaCrea()
    {
        return utf8_encode($this->usuaCrea);
    }

    /**
     * Set fechaCrea
     *
     * @param \DateTime $fechaCrea
     * @return CuposAnuales
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
     * @return CuposAnuales
     */
    public function setUsuaModi($usuaModi)
    {
        $this->usuaModi = utf8_decode($usuaModi);

        return $this;
    }

    /**
     * Get usuaModi
     *
     * @return string 
     */
    public function getUsuaModi()
    {
        return utf8_encode($this->usuaModi);
    }

    /**
     * Set fechaModi
     *
     * @param \DateTime $fechaModi
     * @return CuposAnuales
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
    
    public function __toString()
    {
        return $this->getAnio()." - ".utf8_encode($this->getDescripcion());
    }

}
