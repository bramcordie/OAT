<?php

namespace OAT\OATBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use OAT\OATBundle\Entity\Question;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class StatisticsController extends Controller {
    
     /**
     * @Route("/statistics/question")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function questionAction()
    {
    
        $questions = $this->getDoctrine()
                ->getRepository('OATBundle:Question')
                ->findAll();
        $em = $this->getDoctrine()->getEntityManager();
        $questionRatios = Array();
        
        foreach($questions as $question)
        {
                $questionRatios[] = Array('question' => $question, 'ratio' => $question->getRatio());
                $em->persist($question);
        }
        
        $em->flush();
        
        return $this->render('OATBundle:Statistics:question.html.twig', array('questions' => $questionRatios,));
    }
    
    /**
     * @Route("/statistics/questionjson")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function questionJsonAction()
    {
        $questions = $this->getDoctrine()
                ->getRepository('OATBundle:Question')
                ->findAll();
        
        $encodeThis = array();
        foreach($questions as $question)
        {
            $encodeThis[] = Array('questionID' => $question->getId(), 'ratio' => $question->getRatio());
        }
        
        $response = new Response(json_encode($encodeThis));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * @Route("/statistics/assessment/")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function assessmentAction()
    {
        $assessments = $this->getDoctrine()
                ->getRepository('OATBundle:Assessment')
                ->findBy(array('status' => 1));
        
        $startDateString = ""; 
        $endDateString = "";
        // if the user posted something he probably sent a date along.
         if ($this->getRequest()->getMethod() == 'POST') {
            $startDate = \DateTime::createFromFormat('d/m/Y', $this->getRequest()->request->get('startDate'));
            $endDate = \DateTime::createFromFormat('d/m/Y', $this->getRequest()->request->get('endDate'));
            
            if($endDate == null){
                $endDate = new \DateTime;
            }
            
            $categoryAssessments = $this->getDoctrine()
                ->getRepository('OATBundle:CategoryAssessment')
                ->createQueryBuilder('c') 
                ->where("c.status = 1")
                ->andWhere("c.created <= :endDate")
                ->andWhere("c.created >= :startDate")
                ->setParameters(array('startDate' => $startDate, 'endDate' => $endDate))
                ->getQuery() 
                ->getResult();
            
            $startDateString = ($startDate ? $startDate->format('d/m/Y') : "");
            $endDateString = ($endDate ? $endDate->format('d/m/Y') : "");
         }else{
             $categoryAssessments = $this->getDoctrine()
                ->getRepository('OATBundle:CategoryAssessment')
                ->findBy(array('status' => 1));
         }
        
        $categories = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->findAll();
        
        $categoryLevels = Array();
        
        foreach($categories as $category)
        {
            $categoryLevels[$category->getName()] = Array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 'total' => 0, 'id' => $category->getId());
        }
        
        foreach($categoryAssessments as $category){
            $categoryLevels[$category->getCategory()->getName()][$category->getLevel()]++;
            $categoryLevels[$category->getCategory()->getName()]['total']++;
        }
        
        $foundAssessments = !empty($categoryAssessments);
        
        return $this->render('OATBundle:Statistics:assessment.html.twig', array('assessments' => $assessments,
                                                                                    'categoryLevels' => $categoryLevels,
                                                                                    'startDate' => $startDateString,
                                                                                    'endDate' => $endDateString,
                                                                                    'foundAssessments' => $foundAssessments,));
    }

}
?>
