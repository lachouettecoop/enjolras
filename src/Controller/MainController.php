<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Subject;
use App\Entity\Vote as EnjolrasVote;
use Condorcet\Election;
use Condorcet\Vote;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

class MainController  extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(EntityManagerInterface $em, LdapController $ldapController)
    {

        return $this->render('Main/index.html.twig', array());
    }

    /**
     * @Route("/details", name="app_details")
     */
    public function details(EntityManagerInterface $em, LdapController $ldapController)
    {

        return $this->render('Main/details.html.twig', array());
    }

    /**
     * @Route("/orga", name="app_orga")
     */
    public function orga(EntityManagerInterface $em, LdapController $ldapController)
    {

        return $this->render('Main/organiserVote.html.twig', array());
    }

    /**
     * @Route("/compte", name="app_account")
     */
    public function email(EntityManagerInterface $em, LdapController $ldapController)
    {
        $user = $this->getUser();
        return $this->render('Main/account.html.twig', []);
    }

    /**
     * @Route("/sujets", name="glukose_enjolras_subjects")
     */
    public function showAllSubjectsAction(EntityManagerInterface $em)
    {
        //get all the subjects
        $repository = $em->getRepository('App:Subject');

        $invisibles = $repository->findBy(['termine'=> false, 'visible' => false]);
        $subjects = $repository->findBy(['termine'=> false, 'visible' => true]);
        $subjectsTermine = $repository->findBy(['termine'=> true, 'visible' => true]);

        return $this->render('Main/showAllSubjects.html.twig',[
                'subjects' => $subjects,
                'invisibles' => $invisibles,
                'subjectsTermine' => $subjectsTermine
            ]
        );
    }


    /**
     * @ParamConverter(class="App\Entity\Subject")
     * @Route("/sujet/{id}", name="glukose_enjolras_oneSubject")
     */
    public function showSubjectAction(Subject $subject, EntityManagerInterface $em, AuthorizationCheckerInterface $authorizationChecker,  Request $request)
    {

        if($subject->getVisible() or $authorizationChecker->isGranted('ROLE_ADMIN')){
            $repositoryV = $em->getRepository('App:Vote');
            $repositoryS = $em->getRepository('App:Solution');
            $repoA = $em->getRepository('App:Argument');
            $tabResults = [];

            $user = $this->getUser();
            $vote = $repositoryV->findVoteByUserSubject($subject->getId(), $user->getId());

            $argumentsTab['comprendre'] = $repoA->findBy(['type' => 'comprendre', 'subject'=> $subject]);
            $argumentsTab['pour'] = $repoA->findBy(['type' => 'pour', 'subject'=> $subject]);
            $argumentsTab['contre'] = $repoA->findBy(['type' => 'contre', 'subject'=> $subject]);
            //if simple vote
            if($subject->getVoteSimple()){
                //compute result for graphic chart
                if($subject->getTermine()){
                    foreach($subject->getSolutions() as $solution){
                        $tabResults[] = [$solution, $repositoryV->findVotesBySubjectVote($subject->getId(), $solution->getTitle())];
                    }
                }
                return $this->render('Main/showSubject.html.twig', ['subject' => $subject, 'tabResults' => $tabResults, 'vote' => $vote, 'arguments' => $argumentsTab]);

            } else {
                //else condorcet classement
                //Display a readable vote for the user
                $classement = explode(">", $vote);
                $solutions = array();
                foreach ($classement as $idSolution){
                    $solutions[] = $repositoryS->find($idSolution);
                }

                return $this->render('Main/showSubject.html.twig', ['subject' => $subject, 'vote'   => $vote, 'solutions' => $solutions, 'arguments' => $argumentsTab]);
            }
        } else { throw new NotFoundHttpException('Erreur non disponible'); }
    }

    /**
     * @ParamConverter(class="App\Entity\Subject")
     * @Route("/sujet/vote/resultats/{id}", name="glukose_enjolras_publication_resultats")
     */
    public function publicationResultatAction(Subject $subject, EntityManagerInterface $em){

        $now = new \DateTime('now');
        if($subject->getDateFin() < $now){

            $solutions = $subject->getSolutions();
            $votes = $subject->getVotes();
            $solutionsArray = [];

            if($subject->getVoteSimple()){
                return $this->render('Main/showResults.html.twig', ['subject' => $subject, 'votes'   => $votes, 'solutions'   => $solutionsArray, 'pairs' => []]);
            }

            foreach($solutions as $candidat){
                $id = $candidat->getId();
                $solutionsArray[$id] = $candidat;
            }

            //The Election Object
            $condorcet = new Election();

            foreach($solutions as $candidat){
                $condorcet->addCandidate($candidat->getId());
            }

            /** @var EnjolrasVote $vote */
            foreach($votes as $vote){
                if($vote->getVote() != ''){
                    $condorcet->addVote(new Vote($vote->getVote()));
                }
            }

            $pairs = $condorcet->getPairwise();

            return $this->render('Main/showResults.html.twig', ['subject' => $subject, 'votes'   => $votes, 'solutions'   => $solutionsArray, 'pairs' => $pairs]);
        } else {
            throw new NotFoundHttpException("Résultats non disponibles !");
        }
    }

    /**
     * @ParamConverter(class="App\Entity\Subject")
     * @Route("/sujet/vote/{id}", name="glukose_enjolras_voteCondorcet")
     */
    public function voteAction(Subject $subject, EntityManagerInterface $em, Request $request, \Swift_Mailer $mailer)
    {
        $user = $this->getUser();

        $repositoryV = $em->getRepository('App:Vote');
        $vote = $repositoryV->findVoteByUserSubject($subject->getId(), $user->getId());

        if($vote != null){
            $em->remove($vote);
            $em->flush();
            $this->addFlash('success','Votre précédent vote a été effacé !');
        }

        if($subject){
            //If submited vote
            if ($request->isMethod('POST')) {
                //get vote
                $voteString = $request->get('myvote');
                if($voteString == ''){
                    $this->addFlash('danger','Votre vote ne peut pas être vide ! Merci de recommencer');
                } else {
                    //substring last char
                    $voteString = rtrim($voteString, ">");

                    if(!$user->getTokenAnon() != ''){
                        $random = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1,10))), 1, 10);
                        $user->setTokenAnon($random);
                    }
                    //New vote
                    $enjolrasVote = new EnjolrasVote();
                    $enjolrasVote->setVote($voteString);
                    $enjolrasVote->setSubject($subject);
                    $enjolrasVote->setUser($user);

                    $em->persist($enjolrasVote);
                    $em->persist($user);
                    $em->flush();

                    //Flash message
                    $this->addFlash('notice','Merci de votre vote. Le résultat sera affiché sur cette page lors de la clôture du vote.');

                    $message = (new \Swift_Message('✅ Vote enregistré - La Chouette Coop'))
                        ->setFrom('cdoj@lachouettecoop.fr')
                        ->setTo($user->getEmail())
                        ->setContentType("text/html")
                        ->setBody($this->renderView('Email/vote_confirmation_email.html.twig', ['vote' => $enjolrasVote]))
                    ;
                    $mailer->send($message);

                    //redirect to subject
                    return $this->redirectToRoute("glukose_enjolras_oneSubject", ['id' => $subject->getId()]);
                }
            }

            //return voting view
            if($subject->getVoteSimple()){
                return $this->render('Main/voteBinary.html.twig', ['subject' => $subject] );
            }else{
                return $this->render('Main/voteCondorcet.html.twig', ['subject' => $subject] );
            }

        }else{
            throw new NotFoundHttpException("Page not found");
        }
    }


    /**
     * @ParamConverter(class="App\Entity\Subject")
     * @Route("/sujet/vote/cloture/condorcet/{id}", name="glukose_enjolras_cloture_vote")
     */
    public function clotureCondorcetAction(Subject $subject, EntityManagerInterface $em)
    {
        $repositoryV = $em->getRepository('App:Vote');
        $repositoryS = $em->getRepository('App:Solution');

        $solutions = $subject->getSolutions();
        $votes = $subject->getVotes();

        //The Election Object
        $condorcet = new Election();

        foreach($solutions as $candidat){
            $condorcet->addCandidate($candidat->getId());
        }

        /** @var EnjolrasVote $vote */
        foreach($votes as $vote){
            if($vote->getVote() != ''){
                $condorcet->addVote(new Vote($vote->getVote()));
            }
        }

        $gagnant = $condorcet->getWinner();
        $resultLooser = $condorcet->getLoser();

        $solutionWinner = $repositoryS->find($gagnant->getName());
        $subject->setGagnant($solutionWinner);

        $em->persist($subject);
        $em->flush();

        return $this->render('Main/index.html.twig');
    }


    /**
     * @Route("/commentaire/ajout", name="app_commentaire_ajout")
     */
    public function addCommentaire(EntityManagerInterface $em, Request $request)
    {
        $response = new JsonResponse();

        if($request->isXmlHttpRequest()) {

            $commentaireTxt = $request->request->get('commentaire');
            $idEntity = $request->request->get('identty');

            $commentaire = new Commentaire();
            $commentaire->setUser($this->getUser());
            $commentaire->setType('subject');
            $commentaire->setCommentaire(nl2br($commentaireTxt));
            $commentaire->setVisible(true);

            $subject = $em->getRepository('App:Subject')->find($idEntity);
            if($subject){
                $commentaire->setSubject($subject);
                $em->persist($commentaire);
                $em->flush();
                $response->setStatusCode(200);
                $response->setData(array('successMessage' => "Votre message a bien été envoyé "));
                return $response;
            }

        }
        $response->setStatusCode(412);
        $response->setData(array('errorMessage' => "Erreur le message n'a pas pu être publié"));
        return $response;
    }


}
