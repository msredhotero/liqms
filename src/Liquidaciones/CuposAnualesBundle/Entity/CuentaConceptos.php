<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * CuentaConceptos
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\CuentaConceptosRepository")
 */
class CuentaConceptos
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
     * @ORM\Column(name="RefCuenta", type="integer")
     */
    private $refCuenta;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdConcepto", type="integer")
     */
    private $idConcepto;


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
     * Set refCuenta
     *
     * @param integer $refCuenta
     * @return CuentaConceptos
     */
    public function setRefCuenta($refCuenta)
    {
        $this->refCuenta = $refCuenta;

        return $this;
    }

    /**
     * Get refCuenta
     *
     * @return integer 
     */
    public function getRefCuenta()
    {
        return $this->refCuenta;
    }

    /**
     * Set idConcepto
     *
     * @param integer $idConcepto
     * @return CuentaConceptos
     */
    public function setIdConcepto($idConcepto)
    {
        $this->idConcepto = $idConcepto;

        return $this;
    }

    /**
     * Get idConcepto
     *
     * @return integer 
     */
    public function getIdConcepto()
    {
        return $this->idConcepto;
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="Cuentas")
     * @ORM\JoinColumn(name="RefCuenta", referencedColumnName="id", nullable=false)
     */
    protected $cuentas;

    /**
     * Set cuentas
     *
     * @param \Liquidaciones\CuposAnualesBundle\Entity\Cuentas $cuentas
     * @return CuentaConceptos
     */
    public function setCuentas(\Liquidaciones\CuposAnualesBundle\Entity\Cuentas $cuentas)
    {
        $this->cuentas = $cuentas;

        return $this;
    }

    /**
     * Get cuentas
     *
     * @return \Liquidaciones\CuposAnualesBundle\Entity\Cuentas 
     */
    public function getCuentas()
    {
        return $this->cuentas;
    }
    
    
}
