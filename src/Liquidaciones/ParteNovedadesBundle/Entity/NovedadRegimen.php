<?php

namespace MinSaludBA\ParteNovedadesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NovedadRegimen
 *
 * @ORM\Table(name="novedades.dbo.novedad_regimen")
 * @ORM\Entity
 */
class NovedadRegimen
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
     * @ORM\ManyToOne(targetEntity="Novedad")
     * @ORM\JoinColumn(name="idNovedad", referencedColumnName="id")
     */
    private $novedad;

    /**
     * @var integer
     *
     * @ORM\Column(name="idRegimen", type="integer")
     */
    private $idRegimen;

    /**
     * @var integer
     *
     * @ORM\Column(name="idAgrupamiento", type="integer")
     */
    private $idAgrupamiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="maximoAnual", type="integer")
     */
    private $maximoAnual;


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
     * Set idRegimen
     *
     * @param integer $idRegimen
     * @return NovedadRegimen
     */
    public function setIdRegimen($idRegimen)
    {
        $this->idRegimen = $idRegimen;
    
        return $this;
    }

    /**
     * Get idRegimen
     *
     * @return integer 
     */
    public function getIdRegimen()
    {
        return $this->idRegimen;
    }

    /**
     * Set idAgrupamiento
     *
     * @param integer $idAgrupamiento
     * @return NovedadRegimen
     */
    public function setIdAgrupamiento($idAgrupamiento)
    {
        $this->idAgrupamiento = $idAgrupamiento;
    
        return $this;
    }

    /**
     * Get idAgrupamiento
     *
     * @return integer 
     */
    public function getIdAgrupamiento()
    {
        return $this->idAgrupamiento;
    }

    /**
     * Set maximoAnual
     *
     * @param integer $maximoAnual
     * @return NovedadRegimen
     */
    public function setMaximoAnual($maximoAnual)
    {
        $this->maximoAnual = $maximoAnual;
    
        return $this;
    }

    /**
     * Get maximoAnual
     *
     * @return integer 
     */
    public function getMaximoAnual()
    {
        return $this->maximoAnual;
    }
    
    /**
     * Get Novedad
     *
     * @return integer 
     */
    public function getNovedad()
    {
        return $this->novedad;
    }
}
