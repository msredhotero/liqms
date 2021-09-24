<?php

namespace Liquidaciones\IntranetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DependenciasI
 *
 * @ORM\Table(name="seguridad.dependenciasi")
 * @ORM\Entity
 */
class DependenciasI
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string", length=20)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;
    
    /**
     * Código de 4 usado por sueldos
     * 
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=4)
     */
    private $codigo;
    
    /**
     * 3 primeros digitos=partido, si es una municiaplidad o delegacion municipal 
     * en este campo esta el codigo de partido de 3 digitos del INDEC.
     * 
     * @var string
     *
     * @ORM\Column(name="estable_id", type="string", length=8)
     */
    private $estable_id;
    
    /**
     * Código que usa el organismo en expedientes.
     * 
     * @var string
     *
     * @ORM\Column(name="codigo_exte", type="string", length=7)
     */
    private $codigo_exte;
    
    /**
     * Código que caratula el organismo.
     * 
     * @var string
     *
     * @ORM\Column(name="entecaratulador", type="integer")
     */
    private $entecaratulador;
    
    /**
     * __toString()
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->getNombre();
    }


    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return DependenciasI
     */
    public function setNombre($nombre)
    {
        $this->nombre = utf8_decode($nombre);
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return utf8_encode($this->nombre);
    }
    
    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return utf8_encode($this->codigo);
    }

    /**
     * Set id
     *
     * @param string $id
     * @return DependenciasI
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     * @return DependenciasI
     */
    public function setCodigo($codigo)
    {
        $this->codigo = utf8_decode($codigo);
    
        return $this;
    }

    /**
     * Set estable_id
     *
     * @param string $estableId
     * @return DependenciasI
     */
    public function setEstableId($estableId)
    {
        $this->estable_id = utf8_decode($estableId);
    
        return $this;
    }

    /**
     * Get estable_id
     *
     * @return string 
     */
    public function getEstableId()
    {
        return utf8_encode($this->estable_id);
    }

    /**
     * Set codigo_exte
     *
     * @param string $codigoExte
     * @return DependenciasI
     */
    public function setCodigoExte($codigoExte)
    {
        $this->codigo_exte = utf8_decode($codigoExte);
    
        return $this;
    }

    /**
     * Get codigo_exte
     *
     * @return string 
     */
    public function getCodigoExte()
    {
        return utf8_encode($this->codigo_exte);
    }

    /**
     * Set entecaratulador
     *
     * @param integer $entecaratulador
     * @return DependenciasI
     */
    public function setEntecaratulador($entecaratulador)
    {
        $this->entecaratulador = $entecaratulador;
    
        return $this;
    }

    /**
     * Get entecaratulador
     *
     * @return integer 
     */
    public function getEntecaratulador()
    {
        return $this->entecaratulador;
    }
}