<?php

namespace OAT\OATBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use OAT\OATBundle\Entity\AssessmentQuestion;
use OAT\OATBundle\Entity\CategoryAssessment;
use OAT\OATBundle\Entity\Question;
use OAT\OATBundle\Entity\AssessmentAnswer;
use OAT\OATBundle\Entity\Assessment;
use Symfony\Component\HttpFoundation\Response;
use \DateTime;
use OAT\OATBundle\Entity\Survey;

class AssessmentController extends Controller {

     /**
     * @Route("/assessment/new")
     */
    public function newAction(Request $request) {
        $categories = Array();
       
        $categoryGroups = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategoryGroup')
                ->findAll();
       
        
        $selectedGroups = Array();
        
        foreach($categoryGroups as $categoryGroup)
        {
            if($request->request->get('category-group-'.$categoryGroup->getId()))
            {
                $selectedGroups[] = $categoryGroup;
                foreach($categoryGroup->getQuestionCategoryGroupMember() as $category)
                {
                    if(!in_array($category->getQuestionCategory(), $categories))
                    {
                        $categories[] = $category->getQuestionCategory();
                    }
                }
            }
        }
        
        if(count($selectedGroups) < 1){
            return $this->redirect($this->generateUrl('oat_oat_assessment_select', array()));
        }
        
        $assessment = new Assessment($categories);
        $assessment->setCreated(new DateTime("now"));
        foreach($selectedGroups as $group)
        {
            $assessment->addQuestionCategoryGroup($group);
        }
       
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($assessment);
        $em->flush();
        
        // you have to flush the assessment object before you can seed it with questions
        $sufficientQuestions = $assessment->getCurrentCategoryAssessment()->seedQuestions($this->getDoctrine());
        
        if($sufficientQuestions){
            $em->persist($assessment);
            $em->flush();

            $firstQuestion = $assessment->getCurrentCategoryAssessment()->getQuestions()->first()->getQuestion();

            return $this->render(
                            'OATBundle:Assessment:new.html.twig'
                            , array('Assessment' => $assessment, 'FirstQuestion' => $firstQuestion, ));
        }else{
            return $this->render('OATBundle:Assessment:error.html.twig', array());
        }
    }
    
    /**
     * @Route("/assessment/select")
     */
    public function selectAction() {
        $categoryGroups = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategoryGroup')
                ->findAll();
        
        $categories = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->findAll();
        
        return $this->render(
                            'OATBundle:Assessment:select.html.twig'
                            , array('categoryGroups' => $categoryGroups,'categories' => $categories));
    }

    /**
     * @Route("/assessment/answer")
     */
    public function checkAnswerAction(Request $request) {
        $answerID = intval($request->request->get('answerID'));
        $id = $request->request->get('assessmentID');

        $assessment = $this->getDoctrine()
                ->getRepository('OATBundle:Assessment')
                ->findOneById($id);
        
        $responseArray['feedback'] = $assessment->processAnswer($answerID);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($assessment);
        $em->flush();
        
        if($responseArray['feedback']['newCategory'])
        {
            $assessment->getCurrentCategoryAssessment()->seedQuestions($this->getDoctrine());
        }
        
        $em->persist($assessment);
        $em->flush();
        
        $nextQuestion = $assessment->getCurrentCategoryAssessment()->getCurrentQuestion();

        if ($nextQuestion == null) {
            $responseArray['feedback']['category']['hasNext'] = false;
        } else {
            $responseArray['nextQuestion'] = $nextQuestion->getEncodeable();
        }

        $response = new Response(json_encode($responseArray));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/assessment/result/{assessmentID}")
     */
    public function resultAction($assessmentID) {
        $assessment = $this->getDoctrine()
                ->getRepository('OATBundle:Assessment')
                ->findOneById($assessmentID);
        
        $radarData = Array();
        
        foreach($assessment->getCategoryAssessments() as $category)
        {
            $radarData['score'][] = $category->getLevel();
        }

        foreach($assessment->getQuestionCategoryGroups() as $group)
        {
            foreach($assessment->getQuestionCategories() as $category)
            {
               $radarData[$group->getName()][] = $group->getGroupMemberScoreByID($category->getId());
            }
        }
        
        if ($assessment) {
            return $this->render(
                            'OATBundle:Assessment:result.html.twig'
                            , array('Assessment' => $assessment, 
                                    'radarData' => $radarData,));
        }
    }
    
    /**
     * @Route("/assessment/survey/{assessmentID}")
     */
    public function surveyAction($assessmentID){
        $assessment = $this->getDoctrine()
                ->getRepository('OATBundle:Assessment')
                ->findOneById($assessmentID);
        
        $survey = new Survey();
        $survey->setAsssessment($assessment);
        $survey->setHoursMaths(0);
        $survey->setHoursScience(0);
        $survey->setStartingYear(2012);

        $form = $this->createFormBuilder($survey)
                ->add('educationType', 'choice', array(
                        'choices'   => array('ASO' => 'Algemeen Secundair Onderwerwijs'
                                            ,'TSO' => 'Technisch Secundair Onderswijs'
                                            ,'BSO' => 'Beroepssecundair Onderwijs'
                                            ,'KSO' => 'Kunstsecundair Onderwijs'
                                            ,'ANDERE' => 'Andere'),
                        'required'  => true,
                        ))
                ->add('futureEducation', 'choice', array(
                        'choices'   => array('Biomedische Laboratoriumtechnologie' => 'Biomedische Laboratoriumtechnologie'
                                            ,'Chemie' => 'Chemie'
                                            ,'Voedings- en dieetkunde' => 'Voedings- en dieetkunde'
                                            ,'onbeslist' => 'ik weet het nog niet'
                                            ),
                        'required'  => true,
                        ))
                ->add('education', 'choice', array(
                        'choices'   => array('Architectuur' => 'Architectuur'
                                            ,'Biotechniek-Wetenschappen' => 'Biotechniek-Wetenschappen'
                                            ,'Beeldende Vorming' => 'Beeldende Vorming'
                                            ,'Biotechnische Wetenschappen' => 'Biotechnische Wetenschappen'
                                            ,'Boekhouden-Informatica' => 'Boekhouden-Informatica'
                                            ,'Chemie' => 'Chemie'
                                            ,'Dans' => 'Dans'
                                            ,'Economie-Moderne Talen' => 'Economie-Moderne Talen'
                                            ,'Economie-Wetenschappen' => 'Economie-Wetenschappen'
                                            ,'Elektronica' => 'Elektronica'
                                            ,'Farmaceutisch Technisch Assistent' => 'Farmaceutisch Technisch Assistent'
                                            ,'Gezondheids- en Welzijnswetenschappen' => 'Gezondheids- en Welzijnswetenschappen'
                                            ,'Grieks-Latijn' => 'Grieks-Latijn'
                                            ,'Grieks-Wetenschappen' => 'Grieks-Wetenschappen'
                                            ,'Haarstilist' => 'Haarstilist'
                                            ,'Handel' => 'Handel'
                                            ,'Hotel' => 'Hotel'
                                            ,'Humane Wetenschappen ' => 'Humane Wetenschappen '
                                            ,'Industriële Wetenschappen' => 'Industriële Wetenschappen'
                                            ,'Latijn-Moderne Talen' => 'Latijn-Moderne Talen'
                                            ,'Latijn-Wetenschappen' => 'Latijn-Wetenschappen'
                                            ,'Latijn-Wiskunde' => 'Latijn-Wiskunde'
                                            ,'Lichamelijke Opvoeding en Sport' => 'Lichamelijke Opvoeding en Sport'
                                            ,'Moderne Talen-Wetenschappen' => 'Moderne Talen-Wetenschappen'
                                            ,'Moderne Talen-Wiskunde' => 'Moderne Talen-Wiskunde'
                                            ,'Onthaal en Public Relations' => 'Onthaal en Public Relations'
                                            ,'Organisatie-Assistentie' => 'Organisatie-Assistentie'
                                            ,'Restaurantbedrijf- en Drakenkennis' => 'Restaurantbedrijf- en Drakenkennis'
                                            ,'Schoonheidsverzorging' => 'Schoonheidsverzorging'
                                            ,'Secretariaat-Talen' => 'Secretariaat-Talen'
                                            ,'Sociale en Technische Wetenschappen' => 'Sociale en Technische Wetenschappen'
                                            ,'Specialiteitenrestaurant' => 'Specialiteitenrestaurant'
                                            ,'Sport-Wetenschappen' => 'Sport-Wetenschappen'
                                            ,'Techniek-Wetenschappen' => 'Techniek-Wetenschappen'
                                            ,'Verkoop en Vertegenwoordiging' => 'Verkoop en Vertegenwoordiging'
                                            ,'Wetenschappen' => 'Wetenschappen'
                                            ,'Wetenschappen-Moderne talen' => 'Wetenschappen-Moderne talen'
                                            ,'Wetenschappen-Sport' => 'Wetenschappen-Sport'
                                            ,'Wetenschappen-Talen' => 'Wetenschappen-Talen'
                                            ,'Wiskunde-Wetenschappen' => 'Wiskunde-Wetenschappen'
                                            ),
                        'required'  => true,
                        ))
                ->add('startingYear')
                ->add('schoolName')
                ->add('hoursMaths')
                ->add('hoursScience')
                ->add('studentName', 'text', array('required' => false))
                ->getForm();
        
        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if($form->isValid()){
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($survey);
                $em->flush();
                return $this->redirect($this->generateUrl('oat_oat_assessment_result', array( 'assessmentID' => $assessment->getId())));
            }
        }
        return $this->render('OATBundle:Assessment:survey.html.twig', array('form' => $form->createView(), 'assessmentID' => $assessmentID,));
  
    }

}

?>
