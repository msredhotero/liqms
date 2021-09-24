<?php

namespace Liquidaciones\IntranetBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Liquidaciones\IntranetBundle\Security\Authentication\Token\IntranetUserToken;
use Liquidaciones\IntranetBundle\Entity\UsuariosI;

class IntranetAuthenticationProvider implements AuthenticationProviderInterface
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token)
    {
        $user = $this->userProvider->loadUserByUsername($token->getUser());

        if ($user && $this->validateUser($user, $token->sessionId)) {
            $authenticatedToken = new IntranetUserToken($user->getRoles());
            $authenticatedToken->setUser($user);

            return $authenticatedToken;
        }

        throw new AuthenticationException('The Intranet authentication failed.');
    }
    
    protected function validateUser(UsuariosI $user, $sessionId)
    {
        return $user->getIdSesion() == $sessionId;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof IntranetUserToken;
    }
}
