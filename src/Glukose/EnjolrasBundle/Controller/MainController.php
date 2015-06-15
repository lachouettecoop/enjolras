<?php

namespace Glukose\EnjolrasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Condorcet\Condorcet;
use Condorcet\Vote;
use Glukose\EnjolrasBundle\Entity\Vote as EnjolrasVote;

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
        //find selected subject
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GlukoseEnjolrasBundle:Subject')
            ;

        $subject = $repository->find($idSubject);

        $condorcet = new Condorcet();
        $condorcet->addCandidate('Wagner');
        $condorcet->addCandidate('Debussy');
        $condorcet->addCandidate('Varese');
        $condorcet->addCandidate('Mozart');

        $vote1 = new Vote ('Mozart>Debussy=Wagner>Varese');
        $vote2 = new Vote ('Debussy>Wagner=Mozart>Varese');
        $vote3 = new Vote ('Varese>Debussy=Mozart>Wagner');
        $vote4 = new Vote ('Mozart>Varese=Wagner>Debussy');
        //$vote5 = new Vote ('Mozart>Varese=Wagner>Debussy');
        //$vote6 = new Vote ('Mozart>Varese=Wagner>Debussy');
        $condorcet->addVote($vote1);
        $condorcet->addVote($vote2);
        $condorcet->addVote($vote3);
        $condorcet->addVote($vote4);
        //$condorcet->addVote($vote5);
        //$condorcet->addVote($vote6);

        $result = $condorcet->getWinner();
        $resultLooser = $condorcet->getLoser();

        if($subject){
            return $this->render('GlukoseEnjolrasBundle:Main:showSubject.html.twig',
                                 array('subject' => $subject, 'result' => $result, 'looser' => $resultLooser)
                                );
        }else{
            throw new NotFoundHttpException("Page not found");
        }                
    }


    /**
     * Vote on a subject using condorcet Method
     * 
     * @param  integer  $idSubject Id of the selected subject
     * @return Response 
     */
    public function voteCondorcetAction($idSubject, Request $request)
    {

        //si l'utilisateur n'est pas authentifié on le renvoi à la page de connexion
        if($this->getUser() == null){
            
            //Route and params --> in session
            $parameters = array('idSubject' => $idSubject);
            $route = "glukose_enjolras_voteCondorcet";            
            $request->getSession()->set('redirectResponseCustom', array('route' => $route, 'parameters' => $parameters));
            
            //redirection to connect form
            return $this->redirect($this->generateUrl('fos_user_security_login'));    
        }

        //find selected subject
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('GlukoseEnjolrasBundle:Subject')
            ;

        $subject = $repository->find($idSubject);


        if($subject){

            //If submited vote
            if ($request->isMethod('POST')) {

                //get vote
                $voteString = $this->get('request')->request->get('myvote');
                //substring last char
                $voteString = rtrim($voteString, ">");

                //New vote
                $enjolrasVote = new EnjolrasVote();
                $enjolrasVote->setVote($voteString);
                $enjolrasVote->setSubject($subject);
                $enjolrasVote->setUser($this->getUser());

                //persisting
                $em = $this->getDoctrine()->getManager();
                $em->persist($enjolrasVote);
                $em->flush();

                //Flash message
                $this->get('session')->getFlashBag()->add('notice','Merci de votre vote. Le résultat sera affiché sur cette page lors de la cloture du vote.');

                //redirect to subject
                return $this->redirect($this->generateUrl("glukose_enjolras_oneSubject", array('idSubject' => $subject->getId())));               
            }

            //return voting view
            return $this->render('GlukoseEnjolrasBundle:Main:voteCondorcet.html.twig',
                                 array('subject' => $subject)
                                );
        }else{
            throw new NotFoundHttpException("Page not found");
        }

    }

}
