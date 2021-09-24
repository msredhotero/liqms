<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CupoEstados
 *
 * @ORM\Table("LiquidacionesWeb.dbo.cupoestados")
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CupoEstadosRepository")
 */
class CupoEstados
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
     * @ORM\Column(name="CupoEstado", type="string", length=50)
     */
    private $cupoEstado;


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
     * @ORM\ManyToOne(targetEntity="Cupos", inversedBy="CupoEstados")
     * @ORM\JoinColumn(name="id", referencedColumnName="RefCupoEstado")
     */
    
    
    /**
     * Set cupoEstado
     *
     * @param string $cupoEstado
     * @return CupoEstados
     */
    public function setCupoEstado($cupoEstado)
    {
        $this->cupoEstado = utf8_decode($cupoEstado);

        return $this;
    }

    /**
     * Get cupoEstado
     *
     * @return string 
     */
    public function getCupoEstado()
    {
        return utf8_encode($this->cupoEstado);
    }
    
    


    
    public function __toString()
    {
        return utf8_encode($this->getCupoEstado());
    }
}
