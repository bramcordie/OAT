<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use OAT\OATBundle\Entity\Question;

/**
 * An answer containing to a Question.
 * 
 * @ORM\Entity
 * @ORM\Table(name="answer")
 */
class Answer
{
    /**
     * id
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * answer text
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    protected $answerText;

    /**
     * question
     *
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $question;


    /**
     * Get id
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set answerText
     * @param text $answerText
     */
    public function setAnswerText($answerText)
    {
        $this->answerText = $answerText;
    }

    /**
     * Get answerText
     *
     * @return text 
     */
    public function getAnswerText()
    {
        return $this->answerText;
    }

    /**
     * Set question
     *
     * @param Question $question
     */
    public function setQuestion(Question $question)
    {
        $this->question = $question;
    }

    /**
     * Get question
     *
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }
}