<?php

namespace Glukose\EnjolrasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Condorcet\Condorcet;
use Condorcet\Election;
use Condorcet\Candidate;
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
        $em = $this
            ->getDoctrine()
            ->getManager();

        $repository = $em->getRepository('GlukoseEnjolrasBundle:Subject');
        $repositoryV = $em->getRepository('GlukoseEnjolrasBundle:Vote');
        $repositoryS = $em->getRepository('GlukoseEnjolrasBundle:Solution');

        $subject = $repository->find($idSubject);

        $user = $this->getUser();
        if($user == null){
            /*$request->getSession()->set('redirectResponse', true);*/
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $vote = $repositoryV->findVoteByUserSubject($idSubject, $user->getId());

        //pour l'affichage du vote
        $classement = explode(">", $vote);
        $solutions = array();
        foreach ($classement as $idSolution){
            $solutions[] = $repositoryS->find($idSolution);
        }


        $condorcet = new Election();
        $condorcet->addCandidate('Wagner');
        $condorcet->addCandidate('Debussy');
        $condorcet->addCandidate('Varese');
        $condorcet->addCandidate('Mozart');

        $vote1 = new Vote ('Mozart>Debussy=Wagner>Varese');
        $vote2 = new Vote ('Debussy>Wagner=Mozart>Varese');
        $vote3 = new Vote ('Varese>Debussy=Mozart>Wagner');
        $vote4 = new Vote ('Mozart>Varese=Wagner>Debussy');

        $condorcet->addVote($vote1);
        $condorcet->addVote($vote2);
        $condorcet->addVote($vote3);
        $condorcet->addVote($vote4);

        $result = $condorcet->getWinner();
        $resultLooser = $condorcet->getLoser();

        if($subject){
            return $this->render('GlukoseEnjolrasBundle:Main:showSubject.html.twig',
                                 array('subject' => $subject,
                                       'vote'   => $vote,
                                       'solutions'   => $solutions,
                                       'result'  => $result, 
                                       'looser'  => $resultLooser)
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
        $user = $this->getUser();
        if($user == null){

            //Route and params --> in session
            $parameters = array('idSubject' => $idSubject);
            $route = "glukose_enjolras_voteCondorcet";            
            $request->getSession()->set('redirectResponseCustom', array('route' => $route, 'parameters' => $parameters));

            //redirection to connect form
            return $this->redirect($this->generateUrl('fos_user_security_login'));    
        }

        //find selected subject
        $em = $this
            ->getDoctrine()
            ->getManager();

        $repository = $em->getRepository('GlukoseEnjolrasBundle:Subject');
        $repositoryV = $em->getRepository('GlukoseEnjolrasBundle:Vote');

        $subject = $repository->find($idSubject);

        $vote = $repositoryV->findVoteByUserSubject($idSubject, $user->getId());

        if($vote != null){
            $em->remove($vote);
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice','Votre précédent vote à été effacé !');    
        }

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
    
    public function clotureCondorcetAction($idSubject){
        
         return $this->render('GlukoseEnjolrasBundle:Main:showSubject.html.twig',
                                 array('subject' => $subject,
                                       'vote'   => $vote,
                                       'solutions'   => $solutions,
                                       'result'  => $result, 
                                       'looser'  => $resultLooser)
                                );
        
    }
    
    
    public function publicationResultatAction($idSubject){
        
         return $this->render('GlukoseEnjolrasBundle:Main:showSubject.html.twig',
                                 array('subject' => $subject,
                                       'vote'   => $vote,
                                       'solutions'   => $solutions,
                                       'result'  => $result, 
                                       'looser'  => $resultLooser)
                                );
        
    }

}
