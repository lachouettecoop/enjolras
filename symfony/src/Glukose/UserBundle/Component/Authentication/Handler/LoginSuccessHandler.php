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
    private static $key;

    public function __construct(Router $router, SecurityContext $security, Session $session)
    {
        $this->router = $router;
        $this->security = $security;
        $this->session = $session;
        self::$key = 'routeAfter';
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {

        /*if($this->session->has('redirectResponseCustom')){
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

        return $response;*/

       //check if the referer session key has been set
        if ($this->session->has( self::$key )) {            

            //set the url based on the link they were trying to access before being authenticated
            $route = $this->session->get( self::$key );

            //remove the session key
            $this->session->remove( self::$key );
            //if the referer key was never set, redirect to a default route

        } else{
            $route = $this->router->generate('glukose_enjolras_homepage');

        }

        return new RedirectResponse($route);
        
    }

}