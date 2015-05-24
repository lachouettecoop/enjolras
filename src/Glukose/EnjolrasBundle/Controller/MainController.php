<?php

namespace Glukose\EnjolrasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * Main action, displays home page
     * 
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('GlukoseEnjolrasBundle:Main:index.html.twig');
    }
}
