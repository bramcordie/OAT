<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A question and his collection of answers.
 * 
 * @ORM\Entity
 * @ORM\Table(name="question")
 */
class Question {

    /**
     * id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * description
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * level
     *
     * @ORM\Column(type="integer")
     * @Assert\Max(limit = 3)
     * @Assert\Min(limit = 1)
     */
    protected $level;

    /**
     * right answers counter
     *
     * @ORM\Column(type="integer")
     */
    protected $rightAnswers = 0;

    /**
     * wrong answers counter
     *
     * @ORM\Column(type="integer")
     */
    protected $wrongAnswers = 0;

    /**
     * question text
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $questionText;

    /**
     * category
     *
     * @ORM\ManyToOne(targetEntity="QuestionCategory", inversedBy="questions")
     * @ORM\JoinColumn(name="questioncategory_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * answers
     *
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     */
    protected $answers;

    /**
     * Constructor
     *
     * Initializes the answers collection.
     *
     */
    public function __construct() {
        $this->answers = new ArrayCollection();
    }

    /**
     * Get encodeable
     * 
     * Get an encodeable friendly version of this question object.
     * 
     * @return mixed[] A collection of arrays representing the question.
     */
    public function getEncodeable() {
        $encodeable = get_object_vars($this);
        $answers = Array();

        foreach ($encodeable['answers'] as $answer) {
            $answers[$answer->getId()] = $answer->getAnswerText();
        }

        $encodeable['answers'] = $answers;
        unset($encodeable['category']);

        return $encodeable;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set level
     *
     * @param integer $level
     */
    public function setLevel($level) {
        $this->level = $level;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * Set questionText
     *
     * @param text $questionText
     */
    public function setQuestionText($questionText) {
        $this->questionText = $questionText;
    }

    /**
     * Get questionText
     *
     * @return text 
     */
    public function getQuestionText() {
        return $this->questionText;
    }

    /**
     * Set category
     *
     * @param OAT\OATBundle\Entity\QuestionCategory $category
     */
    public function setCategory(\OAT\OATBundle\Entity\QuestionCategory $category) {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return OAT\OATBundle\Entity\QuestionCategory 
     */
    public function getCategory() {
        return $this->category;
    }

    /**
     * Add answers
     *
     * @param Answer $answers
     */
    public function addAnswer(Answer $answer) {
        $this->answers[] = $answer;
    }

    /**
     * Get answers
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAnswers() {
        return $this->answers;
    }
    
    /**
     * Get right answer
     * 
     * Get the right answer object.
     * 
     * @return OAT\OATBundle\Entity\Answer
     */
    public function getRightAnswer() {
        return $this->answers->first();
    }
    
    /**
     * Check answer
     * 
     * Check if a given answer is the correct one for this question and update statistics.
     * 
     * @param  int      $answerID   The id of the answer to check.
     * @return boolean              Return true if the answer is correct else false.
     */
    public function checkAnswer($answerID) {
        $correct = $this->answers->first()->getId() == $answerID;
        if($correct){
            $this->setRightAnswers($this->getRightAnswers()+1);
        }
        else{
            $this->setWrongAnswers($this->getWrongAnswers()+1);
        }
        return $correct;
    }

    /**
     * Get ratio
     * 
     * Calculate a ratio comparing the amount of right and wrong answers to this question.
     * 
     * @return int  An integer between -1 and 1, negative meaning more wrong answers, positive meaning more right answers.
     */
    public function getRatio() {
        if (($this->getRightAnswers() + $this->getWrongAnswers()) > 0) {
            $ratio = ( ( $this->getRightAnswers() - $this->getWrongAnswers() ) / ( $this->getRightAnswers() + $this->getWrongAnswers() ) );
            return $ratio;
        }else{
            return 0;
        }
     }
     
     /**
      * Reset ratio
      * 
      * Reset the right and wrong counters.
      */
     public function resetRatio(){
         $this->setRightAnswers(0);
         $this->setWrongAnswers(0);
     }

    /**
     * Set rightAnswers
     *
     * @param integer $rightAnswers
     */
    public function setRightAnswers($rightAnswers) {
        $this->rightAnswers = $rightAnswers;
    }

    /**
     * Get rightAnswers
     *
     * @return integer 
     */
    public function getRightAnswers() {
        return $this->rightAnswers;
    }

    /**
     * Set wrongAnswers
     *
     * @param integer $wrongAnswers
     */
    public function setWrongAnswers($wrongAnswers) {
        $this->wrongAnswers = $wrongAnswers;
    }

    /**
     * Get wrongAnswers
     *
     * @return integer 
     */
    public function getWrongAnswers() {
        return $this->wrongAnswers;
    }
}