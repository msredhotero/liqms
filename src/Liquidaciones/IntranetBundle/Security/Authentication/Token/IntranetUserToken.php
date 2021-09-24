<?php

namespace Liquidaciones\IntranetBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class IntranetUserToken extends AbstractToken
{
    public $sessionId;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // Si el usuario ha iniciado sesi�n, se lo considera autenticado
        $this->setAuthenticated(isset($_SESSION['SessionInitiated']) && $_SESSION['SessionInitiated']);
    }

    public function getCredentials()
    {
        return '';
    }
}
