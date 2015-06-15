<?php

namespace Glukose\UserBundle\Component\Authentication\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{

    protected $router;
    protected $security;

    public function __construct(Router $router, SecurityContext $security, Session $session)
    {
        $this->router = $router;
        $this->security = $security;
        $this->session = $session;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        if($this->session->has('redirectResponseCustom')){
            $tabParam = $this->session->get('redirectResponseCustom');
            $response = new RedirectResponse($this->router->generate($tabParam['route'], $tabParam['parameters']));	
        } 
        else {
            if ($this->security->isGranted('ROLE_USER')) {
                $response = new RedirectResponse($this->router->generate('glukose_enjolras_homepage'));
            }
            
            if ($this->security->isGranted('ROLE_ADMIN')) {
                $response = new RedirectResponse($this->router->generate('sonata_admin_dashboard'));
            }

        }

        return $response;
    }

}