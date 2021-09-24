<?php

namespace Liquidaciones\IntranetBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Liquidaciones\IntranetBundle\Security\Authentication\Token\IntranetUserToken;
use Liquidaciones\IntranetBundle\Entity\UsuariosI;

class IntranetAuthenticationListener implements ListenerInterface 
{

    protected $securityContext;
    protected $authenticationManager;
    protected $login_path;
    protected $nombreUsuario;
    protected $rol;
    protected $codSistema;
    protected $descEstablecimiento;
    protected $IdEstablecimiento;
    protected $Id;
    protected $IdEstablLargo;
    protected $perfilId;
    protected $multidep_roles;
    protected $multidep_multi;
    protected $multidep_actual;

    public function __construct(SecurityContextInterface $securityContext, 
            AuthenticationManagerInterface $authenticationManager, 
            $login_path,
            $nombreUsuario,
            $rol,
            $codSistema,
            $descEstablecimiento,
            $perfilId, 
            $IdEstablecimiento,
            $Id,
            $IdEstablLargo,
            $multidep_roles,
            $multidep_multi,
            $multidep_actual) 
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->login_path = $login_path;
        $this->nombreUsuario = $nombreUsuario;
        $this->rol = $rol;
        $this->codSistema = $codSistema;
        $this->descEstablecimiento = $descEstablecimiento;
        $this->IdEstablecimiento = $IdEstablecimiento;
        $this->Id = $Id;
        $this->IdEstablLargo = $IdEstablLargo;
        $this->perfilId = $perfilId;
        $this->multidep_roles = $multidep_roles;
        $this->multidep_multi = $multidep_multi;
        $this->multidep_actual = $multidep_actual;
    }

    public function handle(GetResponseEvent $event) 
    {
        if (isset($_SESSION['SessionInitiated'])
                && $_SESSION['SessionInitiated']) {

            $token = new IntranetUserToken();
            $token->setUser($_SESSION['Usuario']);
            $token->sessionId = $_SESSION['SessionId'];

            try {
                $returnValue = $this->authenticationManager->authenticate($token);

                if ($returnValue instanceof TokenInterface) {
                    return $this->securityContext->setToken($returnValue);
                } else if ($returnValue instanceof Response) {
                    return $event->setResponse($returnValue);
                }
            } catch (AuthenticationException $e) {
//                     aqu� puedes hacer algunas anotaciones
            }
        // Configuraci�n de la sesi�n para desarrollo local
        } elseif ($this->nombreUsuario) {
            $user = new UsuariosI();
            $user->CodSistema = $this->codSistema;
            $user->DescEstablecimiento = $this->descEstablecimiento;
            $user->IdEstablecimiento = $this->IdEstablecimiento;
            $user->Id = $this->Id;
            $user->IdEstablLargo = $this->IdEstablLargo;
            $user->PerfilId = $this->perfilId;
            $user->setNombreusuario($this->nombreUsuario);
            $user->DescripcionPerfil = $this->rol;
            $user->multidep_roles = $this->multidep_roles;
            $user->multidep_multi = $this->multidep_multi;
            $user->multidep_actual = $this->multidep_actual;
            
            $token = new IntranetUserToken($user->getRoles());
            $token->setAuthenticated(true);
            
            $token->setUser($user);
            return $this->securityContext->setToken($token);
        }

        if ($this->login_path) {
            $response = new RedirectResponse($this->login_path);
        } else {
            $response = new Response();
            $response->setStatusCode(403);
        }
        $event->setResponse($response);
    }

}
