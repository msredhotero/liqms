<?php

namespace Liquidaciones\HaberesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * HAPersonalCopia
 *
 * @ORM\Table("haberes.Copia.HAPersonal")
 * @ORM\Entity(repositoryClass="Liquidaciones\HaberesBundle\Entity\HAPersonalCopiaRepository")
 */
class HAPersonalCopia 
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IdPersonal", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}