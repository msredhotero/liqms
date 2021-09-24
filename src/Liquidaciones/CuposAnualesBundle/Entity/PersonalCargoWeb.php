<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PersonalCargoWeb
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\PersonalCargoWebRepository")
 */
class PersonalCargoWeb
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
     * @ORM\Column(name="idPersonalCargo", type="integer")
     */
    private $idPersonalCargo;

    /**
     * @var string
     *
     * @ORM\Column(name="Apyn", type="string", length=120)
     */
    private $apyn;

    /**
     * @var integer
     *
     * @ORM\Column(name="Legajo", type="integer")
     */
    private $legajo;

    /**
     * @var integer
     *
     * @ORM\Column(name="NroDocumento", type="integer")
     */
    private $nroDocumento;

    /**
     * @var string
     *
     * @ORM\Column(name="Agrupamiento", type="string", length=255)
     */
    private $agrupamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="RegimenEstatutario", type="string", length=255)
     */
    private $regimenEstatutario;

    /**
     * @var string
     *
     * @ORM\Column(name="DepeNombramiento", type="string", length=255)
     */
    private $depeNombramiento;

    /**
     * @var string
     *
     * @ORM\Column(name="DepePago", type="string", length=255)
     */
    private $depePago;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaBajaPreventiva", type="datetime", nullable=true)
     */
    private $fechaBajaPreventiva;
    
    
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
     * Set fechaBajaPreventiva
     *
     * @param datetime $fechaBajaPreventiva
     * @return PersonalCargoWeb
     */
    public function setFechaBajaPreventiva($fechaBajaPreventiva)
    {
        $this->fechaBajaPreventiva = $fechaBajaPreventiva;

        return $this;
    }

    /**
     * Get fechaBajaPreventiva
     *
     * @return datetime 
     */
    public function getFechaBajaPreventiva()
    {
        return $this->fechaBajaPreventiva;
    }
    
    
    
    
    
    /**
     * Set idPersonalCargo
     *
     * @param integer $idPersonalCargo
     * @return PersonalCargoWeb
     */
    public function setIdPersonalCargo($idPersonalCargo)
    {
        $this->idPersonalCargo = $idPersonalCargo;

        return $this;
    }

    /**
     * Get idPersonalCargo
     *
     * @return integer 
     */
    public function getIdPersonalCargo()
    {
        return $this->idPersonalCargo;
    }

    /**
     * Set apyn
     *
     * @param string $apyn
     * @return PersonalCargoWeb
     */
    public function setApyn($apyn)
    {
        $this->apyn = $apyn;

        return $this;
    }

    /**
     * Get apyn
     *
     * @return string 
     */
    public function getApyn()
    {
        return $this->apyn;
    }

    /**
     * Set legajo
     *
     * @param integer $legajo
     * @return PersonalCargoWeb
     */
    public function setLegajo($legajo)
    {
        $this->legajo = $legajo;

        return $this;
    }

    /**
     * Get legajo
     *
     * @return integer 
     */
    public function getLegajo()
    {
        return $this->legajo;
    }

    /**
     * Set nroDocumento
     *
     * @param integer $nroDocumento
     * @return PersonalCargoWeb
     */
    public function setNroDocumento($nroDocumento)
    {
        $this->nroDocumento = $nroDocumento;

        return $this;
    }

    /**
     * Get nroDocumento
     *
     * @return integer 
     */
    public function getNroDocumento()
    {
        return $this->nroDocumento;
    }

    /**
     * Set agrupamiento
     *
     * @param string $agrupamiento
     * @return PersonalCargoWeb
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
     * Set regimenEstatutario
     *
     * @param string $regimenEstatutario
     * @return PersonalCargoWeb
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
     * Set depeNombramiento
     *
     * @param string $depeNombramiento
     * @return PersonalCargoWeb
     */
    public function setDepeNombramiento($depeNombramiento)
    {
        $this->depeNombramiento = $depeNombramiento;

        return $this;
    }

    /**
     * Get depeNombramiento
     *
     * @return string 
     */
    public function getDepeNombramiento()
    {
        return $this->depeNombramiento;
    }

    /**
     * Set depePago
     *
     * @param string $depePago
     * @return PersonalCargoWeb
     */
    public function setDepePago($depePago)
    {
        $this->depePago = $depePago;

        return $this;
    }

    /**
     * Get depePago
     *
     * @return string 
     */
    public function getDepePago()
    {
        return $this->depePago;
    }
}
