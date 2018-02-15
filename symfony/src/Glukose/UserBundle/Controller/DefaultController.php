<?php

namespace Glukose\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GlukoseUserBundle:Default:index.html.twig', array('name' => $name));
    }
}
