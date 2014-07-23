<?php


namespace OAT\OATBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AssessmentRepository extends EntityRepository
{
    public function findQuestionByListIndex($assessmentID, $listIndex){
        return $this->getEntityManager()
            ->createQuery( 'SELECT p FROM OATBundle:AssessmentQuestion p 
                            where assessment_id	= :assessmentID
                            AND listIndex = :listIndex')
            ->setParameter('assessmentID', $assessmentID)
            ->setParameter('listIndex', $listIndex)
            ->getResult();
    }
}