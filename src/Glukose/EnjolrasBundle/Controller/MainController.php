<?php

namespace Glukose\EnjolrasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

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
    
    
    /**
     * Show a list of all the subjects available
     * 
     * @return Response
     */
    public function showAllSubjectsAction()
    {
        //get all the subjects
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GlukoseEnjolrasBundle:Subject')
            ;

        $subjects = $repository->findByTermine(false);
        $subjectsTermine = $repository->findByTermine(true);
        
        return $this->render('GlukoseEnjolrasBundle:Main:showAllSubjects.html.twig',
                             array('subjects' => $subjects,
                                  'subjectsTermine' => $subjectsTermine)
                            );
    }
    
    
    /**
     * Show a subject
     * 
     * @param  integer  $idSubject Id of the selected subject
     * @return Response 
     */
    public function showSubjectAction($idSubject)
    {
        //get all the subjects
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GlukoseEnjolrasBundle:Subject')
            ;

        $subject = $repository->find($idSubject);
        
        if($subject){
            return $this->render('GlukoseEnjolrasBundle:Main:showSubject.html.twig',
                             array('subject' => $subject)
                            );
        }else{
            throw new NotFoundHttpException("Page not found");
        }                
    }
    
}
