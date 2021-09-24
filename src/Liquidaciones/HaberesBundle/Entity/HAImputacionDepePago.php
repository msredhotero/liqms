<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Liquidaciones\HaberesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * HAImputacionDepePago
 *
 * @ORM\Table("haberes.Haberes.HAImputacionDepePago")
 * @ORM\Entity(repositoryClass="Liquidaciones\HaberesBundle\Entity\HAImputacionDepePagoRepository")
 */
class HAImputacionDepePago {
    //put your code here
    /**
     * @var integer
     *
     * @ORM\Column(name="IdImputacionPresupuestaria", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="UsuaModi", type="string", length=80)
     */
    private $usuaModi;
    
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="FechaModi", type="datetime")
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
     * Set refDependencia
     *
     * @param integer $refDependencia
     * @return HAImputacionDepePago
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
     * @return HAImputacionDepePago
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
    
    
    
    /**
     * Set usuaModi
     *
     * @param string $usuaModi
     * @return HAImputacionDepePago
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
     * @return HAImputacionDepePago
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
    
}
