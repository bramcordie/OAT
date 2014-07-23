<?php


namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A instance of a question wrapped with additional information about the assessment it's used for.
 * 
 * @ORM\Entity
 * @ORM\Table()
 */
class AssessmentQuestion
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CategoryAssessment", inversedBy="questions")
     */
    protected $assessment;
    
    /**
     
     * @ORM\ManyToOne(targetEntity="Question")
     */
    protected $question;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    protected $listIndex;
    
    public function __construct($assessment, $question, $listIndex)
    {
        $this->assessment = $assessment;
        $this->question = $question;
        $this->listIndex = $listIndex;
    }

    /**
     * Set index
     *
     * @param integer $index
     */
    public function setIndex($index)
    {
        $this->listIndex = $index;
    }

    /**
     * Get index
     *
     * @return integer 
     */
    public function getIndex()
    {
        return $this->listIndex;
    }

    /**
     * Set assessment
     *
     * @param OAT\OATBundle\Entity\CategoryAssessment $assessment
     */
    public function setAssessment(\OAT\OATBundle\Entity\CategoryAssessment $assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * Get assessment
     *
     * @return OAT\OATBundle\Entity\CategoryAssessment 
     */
    public function getAssessment()
    {
        return $this->assessment;
    }

    /**
     * Set question
     *
     * @param OAT\OATBundle\Entity\Question $question
     */
    public function setQuestion(\OAT\OATBundle\Entity\Question $question)
    {
        $this->question = $question;
    }

    /**
     * Get question
     *
     * @return OAT\OATBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set listIndex
     *
     * @param integer $listIndex
     */
    public function setListIndex($listIndex)
    {
        $this->listIndex = $listIndex;
    }

    /**
     * Get listIndex
     *
     * @return integer 
     */
    public function getListIndex()
    {
        return $this->listIndex;
    }
}