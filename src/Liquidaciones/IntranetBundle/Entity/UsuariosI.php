<?php

namespace Liquidaciones\IntranetBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Liquidaciones\IntranetBundle\Entity\UsuariosI
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Liquidaciones\IntranetBundle\Entity\UsuariosIRepository")
 */
class UsuariosI implements UserInterface, \Serializable, EquatableInterface
{
    /**
     * @var string $nombreusuario
     *
     * @ORM\Column(name="nombreusuario", type="string", length=8)
     * @ORM\Id
     */
    private $nombreusuario;
    
    /**
     * @var string $id_sesion
     *
     * @ORM\Column(name="id_sesion", type="string", length=100)
     */
    private $id_sesion;
    
    public $IdEstablecimiento;
    public $DescEstablecimiento;
    public $IdUsuario;
    public $IdPersona;
    public $IdEstablLargo;
    public $Id;
    public $NroUsuario;
    public $PerfilId;
    public $DescripcionPerfil;
    public $CodSistema;
    public $multidep_roles;
    public $multidep_multi;
    public $multidep_actual;
    public $multidep_actual_descripcion;
    public $multidep_actual_codigo;
    public $multidep_actual_estable_id;

    /**
     * Set nombreusuario
     *
     * @param string $nombreusuario
     * @return UsuariosI
     */
    public function setNombreusuario($nombreusuario)
    {
        $this->nombreusuario = $nombreusuario;
    
        return $this;
    }

    /**
     * Get nombreusuario
     *
     * @return string 
     */
    public function getNombreusuario()
    {
        return $this->nombreusuario;
    }
    
    /**
     * Set id_sesion
     *
     * @param string $id_sesion
     * @return UsuariosI
     */
    public function setIdSession($id_sesion)
    {
        $this->id_sesion = $id_sesion;
    
        return $this;
    }

    /**
     * Get id_sesion
     *
     * @return string 
     */
    public function getIdSesion()
    {
        return $this->id_sesion;
    }

    //
    // Interface UserInterface
    //
    public function eraseCredentials() {
        
    }

    public function getPassword() {
        
    }
    
    public function getDependencias() 
    {
        return array_keys($this->multidep_roles);
    }

    public function getRoles() {

        if ($this->multidep_multi && $this->multidep_actual){
            $roles = array();
            $deps = $this->multidep_roles;
            $actual = $this->multidep_actual;
            foreach ($deps[$actual] as $rol) {
                $roles[] = "ROLE_".$rol;
            }
            return $roles;
        }

        return array($this->PerfilId);
    }

    public function getSalt() {
        
    }

    public function getUsername() {
        return $this->getNombreusuario();
    }

    //
    // Interface Serializable
    //
    public function serialize() {
        return serialize(array(
            $this->nombreusuario,
            $this->id_sesion,
            $this->IdEstablecimiento,
            $this->DescEstablecimiento,
            $this->IdUsuario,
            $this->IdPersona,
            $this->IdEstablLargo,
            $this->Id,
            $this->NroUsuario,
            $this->PerfilId,
            $this->DescripcionPerfil,
            $this->CodSistema,
            $this->multidep_roles,
            $this->multidep_multi,
            $this->multidep_actual,
            $this->multidep_actual_descripcion,
            $this->multidep_actual_codigo,
            $this->multidep_actual_estable_id
        ));
    }

    public function unserialize($serialized) {
        list (
            $this->nombreusuario,
            $this->id_sesion,
            $this->IdEstablecimiento,
            $this->DescEstablecimiento,
            $this->IdUsuario,
            $this->IdPersona,
            $this->IdEstablLargo,
            $this->Id,
            $this->NroUsuario,
            $this->PerfilId,
            $this->DescripcionPerfil,
            $this->CodSistema,
            $this->multidep_roles,
            $this->multidep_multi,
            $this->multidep_actual,
            $this->multidep_actual_descripcion,
            $this->multidep_actual_codigo,
            $this->multidep_actual_estable_id
        ) = unserialize($serialized);
    }

    //
    // Interface EquatableInterface
    //
    public function isEqualTo(UserInterface $user) {
        return $this->nombreusuario === $user->getUsername();
    }
}
