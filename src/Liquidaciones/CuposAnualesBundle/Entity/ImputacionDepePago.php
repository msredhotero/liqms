<?php

namespace Liquidaciones\CuposAnualesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImputacionDepePago
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Liquidaciones\CuposAnualesBundle\Entity\ImputacionDepePagoRepository")
 */
class ImputacionDepePago
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
     * @ORM\Column(name="RefDependencia", type="integer")
     */
    private $refDependencia;

    /**
     * @var integer
     *
     * @ORM\Column(name="RefImputacionPresupuestaria", type="integer")
     */
    private $refImputacionPresupuestaria;


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
     * Set refDependencia
     *
     * @param integer $refDependencia
     * @return ImputacionDepePago
     */
    public function setRefDependencia($refDependencia)
    {
        $this->refDependencia = $refDependencia;

        return $this;
    }

    /**
     * Get refDependencia
     *
     * @return integer 
     */
    public function getRefDependencia()
    {
        return $this->refDependencia;
    }

    /**
     * Set refImputacionPresupuestaria
     *
     * @param integer $refImputacionPresupuestaria
     * @return ImputacionDepePago
     */
    public function setRefImputacionPresupuestaria($refImputacionPresupuestaria)
    {
        $this->refImputacionPresupuestaria = $refImputacionPresupuestaria;

        return $this;
    }

    /**
     * Get refImputacionPresupuestaria
     *
     * @return integer 
     */
    public function getRefImputacionPresupuestaria()
    {
        return $this->refImputacionPresupuestaria;
    }
}
