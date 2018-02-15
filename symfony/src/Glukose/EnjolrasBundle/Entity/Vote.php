<?php

namespace Glukose\EnjolrasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Vote
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Glukose\EnjolrasBundle\Entity\VoteRepository")
 */
class Vote
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="vote", type="string", length=255)
     */
    private $vote;
    
    /**
     * @ORM\ManyToOne(targetEntity="Glukose\EnjolrasBundle\Entity\Subject", inversedBy="votes")
     */
    private $subject;
    
    /**
     * @ORM\ManyToOne(targetEntity="Glukose\UserBundle\Entity\User")
     */
    private $user;
    
    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
 
    public function __toString()
    {
        return $this->vote;
    }

    /**
     * Set vote
     *
     * @param string $vote
     * @return Vote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return string 
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set subject
     *
     * @param \Glukose\EnjolrasBundle\Entity\Subject $subject
     * @return Vote
     */
    public function setSubject(\Glukose\EnjolrasBundle\Entity\Subject $subject = null)
    {
        $this->subject = $subject;
        $subject->addVote($this);
        return $this;
    }

    /**
     * Get subject
     *
     * @return \Glukose\EnjolrasBundle\Entity\Subject 
     */
    public function getSubject()
    {
        return $this->subject;
    }


    /**
     * Set user
     *
     * @param \Glukose\UserBundle\Entity\User $user
     * @return Vote
     */
    public function setUser(\Glukose\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Glukose\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Vote
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Vote
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
