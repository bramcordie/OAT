<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Contains all information about an assessment.
 * 
 * @ORM\Entity
 * @ORM\Table(name="assessment")
 */
class Assessment {

    /**
     * id
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * category assessments
     * 
     * @ORM\ManyToMany(targetEntity="CategoryAssessment", cascade={"persist", "remove"})
     */
    protected $categoryAssessments;

    /**
     * question categories
     * 
     * @ORM\ManyToMany(targetEntity="QuestionCategory")
     */
    protected $questionCategories;
    
    /**
     * question category groups
     * 
     * @ORM\ManyToMany(targetEntity="QuestionCategoryGroup")
     */
    protected $questionCategoryGroups;

    /**
     * current category assessment
     * 
     * @ORM\OneToOne(targetEntity="CategoryAssessment", cascade={"persist", "remove"})
     */
    protected $currentCategoryAssessment;

    /**
     * status
     * 
     * @ORM\Column(type="integer")
     */
    protected $status = 0;
    
    /**
     * creation date
     * 
     * @ORM\Column(type="datetime")
     */
    protected $created = 0;
    
    public function __construct(Array $categories = Array()) {
        $this->questionCategories = new ArrayCollection();
        $this->categoryAssessments = new ArrayCollection();
        $this->questionCategoryGroups = new ArrayCollection();

        foreach ($categories as $category) {
            $this->addQuestionCategory($category);
        }

        $this->initCategoryAssessment();
    }
    
    /**
     * Initialize category assessment
     * 
     * Initializes the current CategoryAssessment with the first QuestionCategory.
     */
    public function initCategoryAssessment() {
        $this->setCurrentCategoryAssessment(new CategoryAssessment($this->getQuestionCategories()->first()));
    }

    /**
     * Next category assessment
     * 
     * Considers the current CategoryAssessment completed and swaps in the next one if available.
     * 
     * @return  boolean Returns true if there are any categories left to assess else returns false.
     */
    public function nextCategoryAssessment() {
        $this->addCategoryAssessment($this->getCurrentCategoryAssessment());
        if ($this->getCategoryAssessments()->count() < $this->getQuestionCategories()->count()) {
            $this->setCurrentCategoryAssessment(
                    new CategoryAssessment
                            ($this->getQuestionCategories()
                                    ->offsetGet($this->getCategoryAssessments()->count())
                    )
            );
            return true;
        }
        return false;
    }

    /**
     * Process answer
     * 
     * Delegates the submitted answerID to the active CategoryAssessment, and checks if it's completed and if there's a next one.
     * 
     * @param   int     $answerID   The ID of the user's answer to the current question.
     * @return  mixed[]             Array containing feedback about the current state of the assessment.
     */
    public function processAnswer($answerID) {
        $feedback['category'] = $this->getCurrentCategoryAssessment()->processAnswer($answerID);
        if ($feedback['category']['status'] == 1) {
            if ($this->nextCategoryAssessment()) {
                $feedback['newCategory'] = true;
                $feedback['catName'] = $this->getCurrentCategoryAssessment()->getCategory()->getName();
                $feedback['category']['status'] = 0;
            } else {
                $feedback['newCategory'] = false;
                $feedback['category']['status'] = 1;
                $this->setStatus(1);
            }
        } else {
            $feedback['newCategory'] = false;
            $feedback['category']['status'] = 0;
        }

        return $feedback;
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
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Add categoryAssessments
     *
     * @param OAT\OATBundle\Entity\CategoryAssessment $categoryAssessments
     */
    public function addCategoryAssessment(\OAT\OATBundle\Entity\CategoryAssessment $categoryAssessments) {
        $this->categoryAssessments[] = $categoryAssessments;
    }

    /**
     * Get categoryAssessments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategoryAssessments() {
        return $this->categoryAssessments;
    }

    /**
     * Add questionCategories
     *
     * @param OAT\OATBundle\Entity\QuestionCategory $questionCategories
     */
    public function addQuestionCategory(\OAT\OATBundle\Entity\QuestionCategory $questionCategories) {
        $this->questionCategories[] = $questionCategories;
    }

    /**
     * Get questionCategories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getQuestionCategories() {
        return $this->questionCategories;
    }

    /**
     * Set currentCategoryAssessment
     *
     * @param OAT\OATBundle\Entity\CategoryAssessment $currentCategoryAssessment
     */
    public function setCurrentCategoryAssessment(\OAT\OATBundle\Entity\CategoryAssessment $currentCategoryAssessment) {
        $this->currentCategoryAssessment = $currentCategoryAssessment;
    }

    /**
     * Get currentCategoryAssessment
     *
     * @return OAT\OATBundle\Entity\CategoryAssessment 
     */
    public function getCurrentCategoryAssessment() {
        return $this->currentCategoryAssessment;
    }


    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get categories
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add questionCategoryGroups
     *
     * @param OAT\OATBundle\Entity\QuestionCategoryGroup $questionCategoryGroups
     */
    public function addQuestionCategoryGroup(\OAT\OATBundle\Entity\QuestionCategoryGroup $questionCategoryGroups)
    {
        $this->questionCategoryGroups[] = $questionCategoryGroups;
    }

    /**
     * Get questionCategoryGroups
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getQuestionCategoryGroups()
    {
        return $this->questionCategoryGroups;
    }
    
    /**
     * Get category names
     *
     * @return string[] An array containing all the names of QuestionCategories selected for this Assessment.
     */
    public function getCategoryNames(){
        $names = Array();
        
        foreach($this->getQuestionCategories() as $category)
        {
            $names[] = $category->getName();
        }
        return $names;
    }
}