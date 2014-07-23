<?php

namespace OAT\OATBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * A group of question categories.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class QuestionCategoryGroup
{
    /**
     * id
     *
     * @var integer $id
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * name
     *
     * @var string $name
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * question category group member
     *
     * @var QuestionCategoryGroupMember[] $questionCategoryGroupMember
     * @ORM\OneToMany(targetEntity="QuestionCategoryGroupMember" , mappedBy="questionCategoryGroup")
     */
    protected $questionCategoryGroupMember;

    public function __construct()
    {
        $this->questionCategoryGroupMember = new ArrayCollection();
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
     * Add questionCategoryGroupMember
     *
     * @param OAT\OATBundle\Entity\QuestionCategoryGroupMember $questionCategoryGroupMember
     */
    public function addQuestionCategoryGroupMember(\OAT\OATBundle\Entity\QuestionCategoryGroupMember $questionCategoryGroupMember)
    {
        $this->questionCategoryGroupMember[] = $questionCategoryGroupMember;
    }

    /**
     * Get questionCategoryGroupMember
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getQuestionCategoryGroupMember()
    {
        return $this->questionCategoryGroupMember;
    }
    
    /**
     * Get group member target score by id
     * 
     * Get the target score of the given CategoryGroup if found.
     * 
     * @param   int     $id     The id of the CategoryGroup.
     * @return  int             The target score if the CategoryGroup was found else 0;
     */
    public function getGroupMemberScoreByID($id)
    {
        foreach($this->getQuestionCategoryGroupMember() as $member)
        {
            if($member->getQuestionCategory()->getId() == $id)
            {
                return $member->getTargetScore();
            }
            
        }
        return 0;
    }
}