<?php

namespace OAT\OATBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use OAT\OATBundle\Entity\Question;
use OAT\OATBundle\Entity\Answer;
use OAT\OATBundle\Entity\QuestionMedia;

class QuestionController extends Controller {

    /**
     * @Route("/question/create")
     * @Template()
     * @Secure(roles="ROLE_LECTOR")
     */
    public function createAction(Request $request) {
        $question = new Question();
        $question->setLevel(2);

        $form = $this->createFormBuilder($question)
            ->add('description', 'text')
            ->add('questionText', 'textarea')
            ->add('level', 'integer')
            ->add('category', 'entity', array('class' => 'OATBundle:QuestionCategory',))
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            $answers = array();
            for ($i = 0; (strlen($request->request->get('answer-'.$i))>0) ; $i++) {
                $answers[$i] = new Answer();
                $answers[$i]->setAnswerText($request->request->get('answer-'.$i));
                $answers[$i]->setQuestion($question);
            }

            if ($form->isValid() && count($answers) > 2 ){
                $em = $this->getDoctrine()->getEntityManager();
                foreach($answers as $answer){
                    $em->persist($answer);
                }
                $em->persist($question);
                $em->flush();

                return $this->redirect($this->generateUrl('oat_oat_question_show', array( 'id' => $question->getId())));
            }
        }
        return $this->render('OATBundle:Question:create.html.twig', array('form' => $form->createView(), 'answers' => false));
    }

    /**
     * @Route("/question/edit/{id}")
     * @Template()
     * @Secure(roles="ROLE_LECTOR")
     */
    public function editAction($id, Request $request) {
        $question = $this->getDoctrine()
            ->getRepository('OATBundle:Question')
            ->find($id);

        if (!$question) {
            throw $this->createNotFoundException('Question with id: ' . $id . ' not found!');
        }

        $form = $this->createFormBuilder($question)
            ->add('description', 'text')
            ->add('questionText', 'textarea')
            ->add('level', 'integer')
            ->add('category', 'entity', array('class' => 'OATBundle:QuestionCategory',))
            ->getForm();

        $oldAnswers = $question->getAnswers();

        $edit = true;

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            $answers = array();
            for ($i = 0; (strlen($request->request->get('answer-'.$i))>0) ; $i++) {
                $answers[$i] = new Answer();
                $answers[$i]->setAnswerText($request->request->get('answer-'.$i));
                $answers[$i]->setQuestion($question);
            }

            if ($form->isValid() && count($answers) > 2 ){
                $em = $this->getDoctrine()->getEntityManager();

                //remove the original answers to prevent dulpicates
                foreach($oldAnswers as $oldAnswer){
                    $em->remove($oldAnswer);
                    $question->getAnswers()->removeElement($oldAnswer);
                }

                //itterate all the answers and persist them
                foreach($answers as $answer){
                    $em->persist($answer);
                }
                $em->persist($question);
                $em->flush();

                return $this->redirect($this->generateUrl('oat_oat_question_show', array( 'id' => $question->getId())));
            }
        }

        return $this->render('OATBundle:Question:create.html.twig', array('form' => $form->createView(), 'answers' => $oldAnswers, 'edit' => $edit));
    }

    /**
     * @Route("/question/{id}")
     * @Template()
     */
    public function showAction($id) {
        $question = $this->getDoctrine()
            ->getRepository('OATBundle:Question')
            ->find($id);

        if (!$question) {
            throw $this->createNotFoundException('Question with id: ' . $id . ' not found!');
        }

        return $this->render('OATBundle:Question:show.html.twig',
            array('question' => $question)
        );
    }

    /**
     * @Route("/question/delete/{id}")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id) {
        $question = $this->getDoctrine()
            ->getRepository('OATBundle:Question')
            ->find($id);

        if (!$question) {
            throw $this->createNotFoundException('Question with id: ' . $id . ' not found!');
        }

        $em = $this->getDoctrine()->getEntityManager();

        $assessmentQuestions = $this->getDoctrine()
            ->getRepository('OATBundle:AssessmentQuestion')
            ->findBy( array("question" => $id));

        foreach($assessmentQuestions as $assessmentQuestion){
            $em->remove($assessmentQuestion);
        }
        $em->flush();

        $em->remove($question);
        $em->flush();

        return $this->redirect($this->generateUrl('oat_oat_question_list'));
    }

    /**
     * @Route("/questions")
     * @Template()
     */
    public function listAction() {
        $questions = $this->getDoctrine()
            ->getRepository('OATBundle:Question')
            ->findAll();

        if (!$questions) {
            throw $this->createNotFoundException('There are no questions!');
        }

        return $this->render('OATBundle:Question:list.html.twig', array('questions' => $questions));
    }

    /**
     * @Route("/questions/seed/{categoryID}")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function seedAction($categoryID) {

        $category = $this->getDoctrine()
            ->getRepository('OATBundle:QuestionCategory')
            ->findOneById($categoryID);

        $em = $this->getDoctrine()->getEntityManager();

        for($level = 1; $level <= 3; $level++){
            for($iQuestion = 1; $iQuestion <=10; $iQuestion++)
            {
                $question = new Question();
                $question->setCategory($category);
                $question->setLevel($level);
                $question->setDescription("Dummy Question ".$iQuestion);
                $question->setQuestionText($category->getName()." ".$level." ".$iQuestion);
                for($an = 1; $an <=4; $an++){
                    $answer = new Answer();
                    $answer->setAnswerText("I'm answer nr.".$an);
                    $answer->setQuestion($question);
                    $em->persist($answer);
                }

                $em->persist($question);
            }
        }
        $em->flush();
        return $this->redirect($this->generateUrl('oat_oat_question_list'));
    }


    /**
     * @Route("/media/upload")
     * @Template()
     * @Secure(roles="ROLE_LECTOR")
     */
    public function mediaUploadAction(){

        $media = new QuestionMedia();

        $form = $this->createFormBuilder($media)
            ->add('description')
            ->add('file')
            ->getForm();

        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $media->upload();

                $em->persist($media);
                $em->flush();

                return $this->redirect($this->generateUrl('oat_oat_question_medialist'));
            }
        }
        return $this->render('OATBundle:Question:mediaupload.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/media")
     * @Template()
     * @Secure(roles="ROLE_LECTOR")
     */
    public function mediaListAction(){

        $media = $this->getDoctrine()
            ->getRepository('OATBundle:QuestionMedia')
            ->findAll();

        return $this->render('OATBundle:Question:medialist.html.twig', array('media' => $media));
    }
}

?>
