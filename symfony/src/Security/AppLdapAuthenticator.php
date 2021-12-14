<?php

namespace App\Security;

use App\Controller\LdapController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppLdapAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $ldapController;
    private $userRepository;
    private $em;
    private $router;
    private $csrfTokenManager;

    public function __construct(LdapController $ldapController,
                                UserRepository $userRepository,
                                EntityManagerInterface $em,
                                RouterInterface $router,
                                CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->ldapController = $ldapController;
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->userRepository->findOneBy(['email' => $credentials['email']]);

        if(!$user){
            //check if user exists in LDAP
            if($this->ldapController->checkUser($credentials['email'])){
                //Add user to the database
                $data = $this->ldapController->getUser($credentials['email']);

                $user = new User();
                $user->setEmail($credentials['email']);
                $user->setNom($data[0]['sn'][0]);
                $user->setPrenom($data[0]['description'][0]);
                $this->em->persist($user);
                $this->em->flush();
            }
        }
        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->ldapController->userAuth($credentials['email'], $credentials['password']);
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_home'));

    }

    protected function getLoginUrl()
    {
        return $this->router->generate('app_login');
    }

}
