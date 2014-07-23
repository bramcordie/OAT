<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A question category group member.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class QuestionCategoryGroupMember {
    
    /**
     * question category group
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="QuestionCategoryGroup", inversedBy="questionCategoryGroupMember") 
     */
    protected $questionCategoryGroup;

    /**
     * question category
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="QuestionCategory")
     */
    protected $questionCategory;
    
    /**
     * target score
     *
     * @ORM\Column(type="integer")
     */
    protected $targetScore = 1;

    /**
     * Set targetScore
     *
     * @param integer $targetScore
     */
    public function setTargetScore($targetScore)
    {
        $targetScore = intval($targetScore);
        if($targetScore < 1 || $targetScore > 3){
            $targetScore = 1;
        }
        $this->targetScore = $targetScore;
    }

    /**
     * Get targetScore
     *
     * @return integer 
     */
    public function getTargetScore()
    {
        return $this->targetScore;
    }

    /**
     * Set questionCategoryGroup
     *
     * @param OAT\OATBundle\Entity\QuestionCategoryGroup $questionCategoryGroup
     */
    public function setQuestionCategoryGroup(\OAT\OATBundle\Entity\QuestionCategoryGroup $questionCategoryGroup)
    {
        $this->questionCategoryGroup = $questionCategoryGroup;
    }

    /**
     * Get questionCategoryGroup
     *
     * @return OAT\OATBundle\Entity\QuestionCategoryGroup 
     */
    public function getQuestionCategoryGroup()
    {
        return $this->questionCategoryGroup;
    }

    /**
     * Set questionCategory
     *
     * @param OAT\OATBundle\Entity\QuestionCategory $questionCategory
     */
    public function setQuestionCategory(\OAT\OATBundle\Entity\QuestionCategory $questionCategory)
    {
        $this->questionCategory = $questionCategory;
    }

    /**
     * Get questionCategory
     *
     * @return OAT\OATBundle\Entity\QuestionCategory 
     */
    public function getQuestionCategory()
    {
        return $this->questionCategory;
    }
}