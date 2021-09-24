<?php


namespace Liquidaciones\HaberesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * HAConceptosValor
 *
 * @ORM\Table("haberes.Haberes.HAConceptosValor")
 * @ORM\Entity(repositoryClass="Liquidaciones\HaberesBundle\Entity\HAConceptosValorRepository")
 */
class HAConceptosValor
{
    /**
     * @var integer
     *
     * @ORM\Column(name="IDConceptoValor", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="RefConcepto", type="integer")
     */
    private $refConcepto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="VigDesde", type="datetime")
     */
    private $vigDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="VigHasta", type="datetime")
     */
    private $vigHasta;

    /**
     * @var integer
     *
     * @ORM\Column(name="MesDeAplicacion", type="smallint")
     */
    private $mesDeAplicacion;

    /**
     * @var string
     *
     * @ORM\Column(name="MontoDesde", type="decimal")
     */
    private $montoDesde;

    /**
     * @var string
     *
     * @ORM\Column(name="MontoHasta", type="decimal")
     */
    private $montoHasta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esDiscapacitado", type="boolean")
     */
    private $esDiscapacitado;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdAgrupamiento", type="integer")
     */
    private $idAgrupamiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="RegHorario", type="smallint")
     */
    private $regHorario;

    /**
     * @var integer
     *
     * @ORM\Column(name="CategDesde", type="smallint")
     */
    private $cateDesde;

    /**
     * @var integer
     *
     * @ORM\Column(name="CategHasta", type="smallint")
     */
    private $cateHasta;

    /**
     * @var integer
     *
     * @ORM\Column(name="AntiguedadDesde", type="smallint")
     */
    private $antiguedadDesde;

    /**
     * @var integer
     *
     * @ORM\Column(name="AntiguedadHasta", type="smallint")
     */
    private $antiguedadHasta;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdPlanta", type="smallint")
     */
    private $idPlanta;

    /**
     * @var string
     *
     * @ORM\Column(name="Monto", type="decimal")
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="Modulos", type="decimal")
     */
    private $modulos;

    /**
     * @var string
     *
     * @ORM\Column(name="Porcentaje", type="decimal")
     */
    private $porcentaje;

    /**
     * @var integer
     *
     * @ORM\Column(name="Cantidad", type="smallint")
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="MontoMinimo", type="decimal")
     */
    private $montoMinimo;

    /**
     * @var string
     *
     * @ORM\Column(name="MontoMaximo", type="decimal")
     */
    private $montoMaximo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdConceptoaux", type="integer")
     */
    private $idConceptoaux;

    /**
     * @var string
     *
     * @ORM\Column(name="Condicion", type="string", length=255)
     */
    private $condicion;

    /**
     * @var string
     *
     * @ORM\Column(name="FormulaIsTrue", type="string", length=255)
     */
    private $formulaIsTrue;

    /**
     * @var string
     *
     * @ORM\Column(name="FormulaIsFalse", type="string", length=255)
     */
    private $formulaIsFalse;

    /**
     * @var string
     *
     * @ORM\Column(name="Perfil", type="string", length=1)
     */
    private $perfil;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdEncasillamiento", type="integer")
     */
    private $idEncasillamiento;

    /**
     * @var integer
     *
     * @ORM\Column(name="Funcion", type="integer")
     */
    private $funcion;

    /**
     * @var integer
     *
     * @ORM\Column(name="RefGrupoDepeAplica", type="integer")
     */
    private $refGrupoDepeAplica;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdAtributo", type="integer")
     */
    private $idAtributo;

    /**
     * @var integer
     *
     * @ORM\Column(name="IdRegimenEstatutario", type="integer")
     */
    private $idRegimenEstatutario;


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
     * Set refConcepto
     *
     * @param integer $refConcepto
     * @return HAConceptosValor
     */
    public function setRefConcepto($refConcepto)
    {
        $this->refConcepto = $refConcepto;

        return $this;
    }

    /**
     * Get refConcepto
     *
     * @return integer 
     */
    public function getRefConcepto()
    {
        return $this->refConcepto;
    }

    /**
     * Set vigDesde
     *
     * @param \DateTime $vigDesde
     * @return HAConceptosValor
     */
    public function setVigDesde($vigDesde)
    {
        $this->vigDesde = $vigDesde;

        return $this;
    }

    /**
     * Get vigDesde
     *
     * @return \DateTime 
     */
    public function getVigDesde()
    {
        return $this->vigDesde;
    }

    /**
     * Set vigHasta
     *
     * @param \DateTime $vigHasta
     * @return HAConceptosValor
     */
    public function setVigHasta($vigHasta)
    {
        $this->vigHasta = $vigHasta;

        return $this;
    }

    /**
     * Get vigHasta
     *
     * @return \DateTime 
     */
    public function getVigHasta()
    {
        return $this->vigHasta;
    }

    /**
     * Set mesDeAplicacion
     *
     * @param integer $mesDeAplicacion
     * @return HAConceptosValor
     */
    public function setMesDeAplicacion($mesDeAplicacion)
    {
        $this->mesDeAplicacion = $mesDeAplicacion;

        return $this;
    }

    /**
     * Get mesDeAplicacion
     *
     * @return integer 
     */
    public function getMesDeAplicacion()
    {
        return $this->mesDeAplicacion;
    }

    /**
     * Set montoDesde
     *
     * @param string $montoDesde
     * @return HAConceptosValor
     */
    public function setMontoDesde($montoDesde)
    {
        $this->montoDesde = $montoDesde;

        return $this;
    }

    /**
     * Get montoDesde
     *
     * @return string 
     */
    public function getMontoDesde()
    {
        return $this->montoDesde;
    }

    /**
     * Set montoHasta
     *
     * @param string $montoHasta
     * @return HAConceptosValor
     */
    public function setMontoHasta($montoHasta)
    {
        $this->montoHasta = $montoHasta;

        return $this;
    }

    /**
     * Get montoHasta
     *
     * @return string 
     */
    public function getMontoHasta()
    {
        return $this->montoHasta;
    }

    /**
     * Set esDiscapacitado
     *
     * @param boolean $esDiscapacitado
     * @return HAConceptosValor
     */
    public function setEsDiscapacitado($esDiscapacitado)
    {
        $this->esDiscapacitado = $esDiscapacitado;

        return $this;
    }

    /**
     * Get esDiscapacitado
     *
     * @return boolean 
     */
    public function getEsDiscapacitado()
    {
        return $this->esDiscapacitado;
    }

    /**
     * Set idAgrupamiento
     *
     * @param integer $idAgrupamiento
     * @return HAConceptosValor
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
     * Set regHorario
     *
     * @param integer $regHorario
     * @return HAConceptosValor
     */
    public function setRegHorario($regHorario)
    {
        $this->regHorario = $regHorario;

        return $this;
    }

    /**
     * Get regHorario
     *
     * @return integer 
     */
    public function getRegHorario()
    {
        return $this->regHorario;
    }

    /**
     * Set cateDesde
     *
     * @param integer $cateDesde
     * @return HAConceptosValor
     */
    public function setCateDesde($cateDesde)
    {
        $this->cateDesde = $cateDesde;

        return $this;
    }

    /**
     * Get cateDesde
     *
     * @return integer 
     */
    public function getCateDesde()
    {
        return $this->cateDesde;
    }

    /**
     * Set cateHasta
     *
     * @param integer $cateHasta
     * @return HAConceptosValor
     */
    public function setCateHasta($cateHasta)
    {
        $this->cateHasta = $cateHasta;

        return $this;
    }

    /**
     * Get cateHasta
     *
     * @return integer 
     */
    public function getCateHasta()
    {
        return $this->cateHasta;
    }

    /**
     * Set antiguedadDesde
     *
     * @param integer $antiguedadDesde
     * @return HAConceptosValor
     */
    public function setAntiguedadDesde($antiguedadDesde)
    {
        $this->antiguedadDesde = $antiguedadDesde;

        return $this;
    }

    /**
     * Get antiguedadDesde
     *
     * @return integer 
     */
    public function getAntiguedadDesde()
    {
        return $this->antiguedadDesde;
    }

    /**
     * Set antiguedadHasta
     *
     * @param integer $antiguedadHasta
     * @return HAConceptosValor
     */
    public function setAntiguedadHasta($antiguedadHasta)
    {
        $this->antiguedadHasta = $antiguedadHasta;

        return $this;
    }

    /**
     * Get antiguedadHasta
     *
     * @return integer 
     */
    public function getAntiguedadHasta()
    {
        return $this->antiguedadHasta;
    }

    /**
     * Set idPlanta
     *
     * @param integer $idPlanta
     * @return HAConceptosValor
     */
    public function setIdPlanta($idPlanta)
    {
        $this->idPlanta = $idPlanta;

        return $this;
    }

    /**
     * Get idPlanta
     *
     * @return integer 
     */
    public function getIdPlanta()
    {
        return $this->idPlanta;
    }

    /**
     * Set monto
     *
     * @param string $monto
     * @return HAConceptosValor
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string 
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set modulos
     *
     * @param string $modulos
     * @return HAConceptosValor
     */
    public function setModulos($modulos)
    {
        $this->modulos = $modulos;

        return $this;
    }

    /**
     * Get modulos
     *
     * @return string 
     */
    public function getModulos()
    {
        return $this->modulos;
    }

    /**
     * Set porcentaje
     *
     * @param string $porcentaje
     * @return HAConceptosValor
     */
    public function setPorcentaje($porcentaje)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje
     *
     * @return string 
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return HAConceptosValor
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set montoMinimo
     *
     * @param string $montoMinimo
     * @return HAConceptosValor
     */
    public function setMontoMinimo($montoMinimo)
    {
        $this->montoMinimo = $montoMinimo;

        return $this;
    }

    /**
     * Get montoMinimo
     *
     * @return string 
     */
    public function getMontoMinimo()
    {
        return $this->montoMinimo;
    }

    /**
     * Set montoMaximo
     *
     * @param string $montoMaximo
     * @return HAConceptosValor
     */
    public function setMontoMaximo($montoMaximo)
    {
        $this->montoMaximo = $montoMaximo;

        return $this;
    }

    /**
     * Get montoMaximo
     *
     * @return string 
     */
    public function getMontoMaximo()
    {
        return $this->montoMaximo;
    }

    /**
     * Set idConceptoaux
     *
     * @param integer $idConceptoaux
     * @return HAConceptosValor
     */
    public function setIdConceptoaux($idConceptoaux)
    {
        $this->idConceptoaux = $idConceptoaux;

        return $this;
    }

    /**
     * Get idConceptoaux
     *
     * @return integer 
     */
    public function getIdConceptoaux()
    {
        return $this->idConceptoaux;
    }

    /**
     * Set condicion
     *
     * @param string $condicion
     * @return HAConceptosValor
     */
    public function setCondicion($condicion)
    {
        $this->condicion = $condicion;

        return $this;
    }

    /**
     * Get condicion
     *
     * @return string 
     */
    public function getCondicion()
    {
        return $this->condicion;
    }

    /**
     * Set formulaIsTrue
     *
     * @param string $formulaIsTrue
     * @return HAConceptosValor
     */
    public function setFormulaIsTrue($formulaIsTrue)
    {
        $this->formulaIsTrue = $formulaIsTrue;

        return $this;
    }

    /**
     * Get formulaIsTrue
     *
     * @return string 
     */
    public function getFormulaIsTrue()
    {
        return $this->formulaIsTrue;
    }

    /**
     * Set formulaIsFalse
     *
     * @param string $formulaIsFalse
     * @return HAConceptosValor
     */
    public function setFormulaIsFalse($formulaIsFalse)
    {
        $this->formulaIsFalse = $formulaIsFalse;

        return $this;
    }

    /**
     * Get formulaIsFalse
     *
     * @return string 
     */
    public function getFormulaIsFalse()
    {
        return $this->formulaIsFalse;
    }

    /**
     * Set perfil
     *
     * @param string $perfil
     * @return HAConceptosValor
     */
    public function setPerfil($perfil)
    {
        $this->perfil = $perfil;

        return $this;
    }

    /**
     * Get perfil
     *
     * @return string 
     */
    public function getPerfil()
    {
        return $this->perfil;
    }

    /**
     * Set idEncasillamiento
     *
     * @param integer $idEncasillamiento
     * @return HAConceptosValor
     */
    public function setIdEncasillamiento($idEncasillamiento)
    {
        $this->idEncasillamiento = $idEncasillamiento;

        return $this;
    }

    /**
     * Get idEncasillamiento
     *
     * @return integer 
     */
    public function getIdEncasillamiento()
    {
        return $this->idEncasillamiento;
    }

    /**
     * Set funcion
     *
     * @param integer $funcion
     * @return HAConceptosValor
     */
    public function setFuncion($funcion)
    {
        $this->funcion = $funcion;

        return $this;
    }

    /**
     * Get funcion
     *
     * @return integer 
     */
    public function getFuncion()
    {
        return $this->funcion;
    }

    /**
     * Set refGrupoDepeAplica
     *
     * @param integer $refGrupoDepeAplica
     * @return HAConceptosValor
     */
    public function setRefGrupoDepeAplica($refGrupoDepeAplica)
    {
        $this->refGrupoDepeAplica = $refGrupoDepeAplica;

        return $this;
    }

    /**
     * Get refGrupoDepeAplica
     *
     * @return integer 
     */
    public function getRefGrupoDepeAplica()
    {
        return $this->refGrupoDepeAplica;
    }

    /**
     * Set idAtributo
     *
     * @param integer $idAtributo
     * @return HAConceptosValor
     */
    public function setIdAtributo($idAtributo)
    {
        $this->idAtributo = $idAtributo;

        return $this;
    }

    /**
     * Get idAtributo
     *
     * @return integer 
     */
    public function getIdAtributo()
    {
        return $this->idAtributo;
    }

    /**
     * Set idRegimenEstatutario
     *
     * @param integer $idRegimenEstatutario
     * @return HAConceptosValor
     */
    public function setIdRegimenEstatutario($idRegimenEstatutario)
    {
        $this->idRegimenEstatutario = $idRegimenEstatutario;

        return $this;
    }

    /**
     * Get idRegimenEstatutario
     *
     * @return integer 
     */
    public function getIdRegimenEstatutario()
    {
        return $this->idRegimenEstatutario;
    }
}
