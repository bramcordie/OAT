<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A collection of questions of a certain category.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class QuestionCategory
{
    /**
     * id
     *
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * name
     *
     * @var string $name
     * @Assert\NotBlank()
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * questions
     *
     * @ORM\OneToMany(targetEntity="Question", mappedBy="category", cascade={"persist", "remove"})
     */
    protected $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add questions
     *
     * @param OAT\OATBundle\Entity\Question $questions
     */
    public function addQuestion(\OAT\OATBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;
    }

    /**
     * Get questions
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}