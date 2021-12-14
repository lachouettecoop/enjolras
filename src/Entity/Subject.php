<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Subject
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Subject
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
 
    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_pleniere", type="date", nullable=true)
     */
    private $datePleniere;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebut", type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
 
    /**
     * @var boolean
     *
     * @ORM\Column(name="termine", type="boolean", nullable=true)
     */
    private $termine;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible = true;
 
    /**
     * @var boolean
     *
     * @ORM\Column(name="voteSimple", type="boolean", nullable=true)
     */
    private $voteSimple;
 
     /**
     * @var string
     *
     * @ORM\Column(name="gagnant", type="string", length=255, nullable=true)
     */
    private $gagnant;
 
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Solution", mappedBy="subject", cascade={"persist"})
     */
    private $solutions;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="subject", cascade={"persist"})
     */
    private $votes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Argument", mappedBy="subject",cascade={"persist"})
     */
    private $arguments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commentaire", mappedBy="subject")
     */
    private $commentaires;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $anonyme;


    public function __toString()
    {
        return $this->title!=''? $this->title:'new sujet';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Subject
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Subject
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     * @return Subject
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string 
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set termine
     *
     * @param boolean $termine
     * @return Subject
     */
    public function setTermine($termine)
    {
        $this->termine = $termine;

        return $this;
    }

    /**
     * Get termine
     *
     * @return boolean 
     */
    public function getTermine()
    {
        return $this->termine;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->solutions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->arguments = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    /**
     * Add solutions
     *
     * @param \App\Entity\Solution $solutions
     * @return Subject
     */
    public function addSolution(\App\Entity\Solution $solutions)
    {
        $this->solutions[] = $solutions;

        return $this;
    }

    /**
     * Remove solutions
     *
     * @param \App\Entity\Solution $solutions
     */
    public function removeSolution(\App\Entity\Solution $solutions)
    {
        $this->solutions->removeElement($solutions);
    }

    /**
     * Get solutions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSolutions()
    {
        return $this->solutions;
    }

    /**
     * Add votes
     *
     * @param \App\Entity\Vote $votes
     * @return Subject
     */
    public function addVote(\App\Entity\Vote $votes)
    {
        $this->votes[] = $votes;

        return $this;
    }

    /**
     * Remove votes
     *
     * @param \App\Entity\Vote $votes
     */
    public function removeVote(\App\Entity\Vote $votes)
    {
        $this->votes->removeElement($votes);
    }

    /**
     * Get votes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return Subject
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set gagnant
     *
     * @param string $gagnant
     * @return Subject
     */
    public function setGagnant($gagnant)
    {
        $this->gagnant = $gagnant;

        return $this;
    }

    /**
     * Get gagnant
     *
     * @return string 
     */
    public function getGagnant()
    {
        return $this->gagnant;
    }

    /**
     * Set voteSimple
     *
     * @param boolean $voteSimple
     * @return Subject
     */
    public function setVoteSimple($voteSimple)
    {
        $this->voteSimple = $voteSimple;

        return $this;
    }

    /**
     * Get voteSimple
     *
     * @return boolean 
     */
    public function getVoteSimple()
    {
        return $this->voteSimple;
    }

    /**
     * Set visible.
     *
     * @param bool|null $visible
     *
     * @return Subject
     */
    public function setVisible($visible = null)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool|null
     */
    public function getVisible()
    {
        return $this->visible;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * @return Collection|Argument[]
     */
    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function addArguments(Argument $arguments): self
    {
        if (!$this->arguments->contains($arguments)) {
            $this->arguments[] = $arguments;
            $arguments->setSubject($this);
        }

        return $this;
    }

    public function removeArguments(Argument $arguments): self
    {
        if ($this->arguments->contains($arguments)) {
            $this->arguments->removeElement($arguments);
            // set the owning side to null (unless already changed)
            if ($arguments->getSubject() === $this) {
                $arguments->setSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setSubject($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->contains($commentaire)) {
            $this->commentaires->removeElement($commentaire);
            // set the owning side to null (unless already changed)
            if ($commentaire->getSubject() === $this) {
                $commentaire->setSubject(null);
            }
        }

        return $this;
    }

    public function getDatePleniere(): ?\DateTimeInterface
    {
        return $this->datePleniere;
    }

    public function setDatePleniere(?\DateTimeInterface $datePleniere): self
    {
        $this->datePleniere = $datePleniere;

        return $this;
    }

    public function addArgument(Argument $argument): self
    {
        if (!$this->arguments->contains($argument)) {
            $this->arguments[] = $argument;
            $argument->setSubject($this);
        }

        return $this;
    }

    public function removeArgument(Argument $argument): self
    {
        if ($this->arguments->contains($argument)) {
            $this->arguments->removeElement($argument);
            // set the owning side to null (unless already changed)
            if ($argument->getSubject() === $this) {
                $argument->setSubject(null);
            }
        }

        return $this;
    }

    public function getAnonyme(): ?bool
    {
        return $this->anonyme;
    }

    public function setAnonyme(?bool $anonyme): self
    {
        $this->anonyme = $anonyme;

        return $this;
    }
}
