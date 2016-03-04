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


        //if simple vote
        if($subject->getVoteSimple()){

            $tabResults = array();

            //compute result for graphic chart
            if($subject->getTermine()){
                foreach($subject->getSolutions() as $solution){
                    $tabResults[] = array($solution, 
                                          $repositoryV->findVotesBySubjectVote($idSubject, $solution->getTitle())
                                         );
                }

            }

            return $this->render('GlukoseEnjolrasBundle:Main:showSubjectSimpleVote.html.twig',
                                 array('subject' => $subject,
                                       'tabResults'   => $tabResults,
                                       'vote'   => $vote
                                      ));            
        } else {

            //else condorcet classement

            //Display a readable vote for the user
            $classement = explode(">", $vote);
            $solutions = array();
            foreach ($classement as $idSolution){
                $solutions[] = $repositoryS->find($idSolution);
            }

            return $this->render('GlukoseEnjolrasBundle:Main:showSubject.html.twig',
                                 array('subject' => $subject,
                                       'vote'   => $vote,
                                       'solutions'   => $solutions)
                                );            
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

        //If not authentified back to connection page
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
            if($subject->getVoteSimple()){
                $view = 'GlukoseEnjolrasBundle:Main:voteBinary.html.twig';
            }else{
                $view = 'GlukoseEnjolrasBundle:Main:voteCondorcet.html.twig';
            }


            return $this->render($view,
                                 array('subject' => $subject)
                                );
        }else{
            throw new NotFoundHttpException("Page not found");
        }

    }

    public function clotureCondorcetAction($idSubject){

        $em = $this
            ->getDoctrine()
            ->getManager();

        $repository = $em->getRepository('GlukoseEnjolrasBundle:Subject');
        $repositoryV = $em->getRepository('GlukoseEnjolrasBundle:Vote');
        $repositoryS = $em->getRepository('GlukoseEnjolrasBundle:Solution');

        $subject = $repository->find($idSubject);

        $solutions = $subject->getSolutions();
        $votes = $subject->getVotes();

        //The Election Object
        $condorcet = new Election();

        foreach($solutions as $candidat){
            $condorcet->addCandidate($candidat->getId());
        }

        foreach($votes as $vote){
            $condorcet->addVote(new Vote ($vote->getVote()));
        }

        $gagnant = $condorcet->getWinner();
        $resultLooser = $condorcet->getLoser();

        $solutionWinner = $repositoryS->find($gagnant->getName());
        $subject->setGagnant($solutionWinner);

        $em->persist($subject);
        $em->flush();

        return $this->render('GlukoseEnjolrasBundle:Main:index.html.twig');


    }


    public function publicationResultatAction($idSubject){

        $em = $this
            ->getDoctrine()
            ->getManager();

        $repository = $em->getRepository('GlukoseEnjolrasBundle:Subject');

        $subject = $repository->find($idSubject);

        $solutions = $subject->getSolutions();
        $votes = $subject->getVotes();

        $solutionsArray = array();

        foreach($solutions as $candidat){ 
            $id = $candidat->getId();
            $solutionsArray[$id] = $candidat;
        }


        return $this->render('GlukoseEnjolrasBundle:Main:showResults.html.twig',
                             array('subject' => $subject,
                                   'votes'   => $votes,
                                   'solutions'   => $solutionsArray)
                            );

    }

}
