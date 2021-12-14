<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solution
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Solution
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
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="pros", type="text", nullable=true)
     */
    private $pros;

    /**
     * @var string
     *
     * @ORM\Column(name="cons", type="text", nullable=true)
     */
    private $cons;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subject", inversedBy="solutions")
     */
    private $subject;


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
        return $this->title;
    }
 
    /**
     * Constructor
     */
    public function __construct($title = '')
    {
        if($title != ''){
            $this->title = $title;
        }
        return $this;

    }

    /**
     * Set title
     *
     * @param string $title
     * @return Solution
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
     * @return Solution
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
     * Set pros
     *
     * @param string $pros
     * @return Solution
     */
    public function setPros($pros)
    {
        $this->pros = $pros;

        return $this;
    }

    /**
     * Get pros
     *
     * @return string 
     */
    public function getPros()
    {
        return $this->pros;
    }

    /**
     * Set cons
     *
     * @param string $cons
     * @return Solution
     */
    public function setCons($cons)
    {
        $this->cons = $cons;

        return $this;
    }

    /**
     * Get cons
     *
     * @return string 
     */
    public function getCons()
    {
        return $this->cons;
    }

    /**
     * Set subject
     *
     * @param \App\Entity\Subject $subject
     * @return Solution
     */
    public function setSubject(\App\Entity\Subject $subject = null)
    {
        $this->subject = $subject;
        $subject->addSolution($this);
        return $this;
    }

    /**
     * Get subject
     *
     * @return \App\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
