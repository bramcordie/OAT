<?php

namespace OAT\OATBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use OAT\OATBundle\Entity\Question;
use OAT\OATBundle\Entity\QuestionCategory;
use OAT\OATBundle\Entity\QuestionCategoryGroup;
use OAT\OATBundle\Entity\Answer;
use OAT\OATBundle\Entity\QuestionCategoryGroupMember;
use JMS\SecurityExtraBundle\Annotation\Secure;

class QuestionCategoryController extends Controller {

    /**
     * @Route("/category/groups")
     * @Template()
     */
    public function grouplistAction() {
        $groups = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategoryGroup')
                ->findAll();
        
        $categories = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->findAll();

        if (!$groups) {
            throw $this->createNotFoundException('There are no questions!');
        }

        return $this->render('OATBundle:QuestionCategory:grouplist.html.twig', array('CategoryGroups' => $groups, "categories" => $categories));
    }

    /**
     * @Route("/categories")
     * @Template()
     */
    public function listAction() {
        $category = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->findAll();

        if (!$category) {
            throw $this->createNotFoundException("Can't find any question catergories!");
        }

        return $this->render('OATBundle:QuestionCategory:list.html.twig', array('Categories' => $category));
    }

    /**
     * @Route("/category/create")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createAction() {

        $questionCategory = new QuestionCategory();

        $form = $this->createFormBuilder($questionCategory)
                ->add('name', 'text')
                ->getForm();

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($questionCategory);
                $em->flush();

                return $this->redirect($this->generateUrl('oat_oat_questioncategory_show', array('id' => $questionCategory->getId())));
            }
        }

        return $this->render('OATBundle:QuestionCategory:create.html.twig', array('form' => $form->createView(),));
    }
    
    /**
     * @Route("/category/group/create")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createGroupAction() {

        $questionCategoryGroup = new QuestionCategoryGroup();

        $form = $this->createFormBuilder($questionCategoryGroup)
                ->add('name', 'text')
                ->getForm();

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($questionCategoryGroup);
                $em->flush();

                return $this->redirect($this->generateUrl('oat_oat_questioncategory_showgroup', array('id' => $questionCategoryGroup->getId())));
            }
        }

        return $this->render('OATBundle:CategoryGroup:create.html.twig', array('form' => $form->createView(),));
    }
    

    /**
     * @Route("/category/{id}")
     * @Secure(roles="ROLE_LECTOR")
     */
    public function showAction($id) {
        $questionCategory = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->find($id);

        if (!$questionCategory) {
            throw $this->createNotFoundException('Question Category with id: ' . $id . ' not found!');
        }

        $levelCounter = Array();
        $levelCounter[0] = 0;
        foreach ($questionCategory->getQuestions() as $question) {
            if (isset($levelCounter[$question->getLevel()])) {
                $levelCounter[$question->getLevel()]++;
            } else {
                $levelCounter[$question->getLevel()] = 1;
            }
            $levelCounter[0]++;
        }
        ksort($levelCounter);

        return $this->render('OATBundle:QuestionCategory:show.html.twig', array('category' => $questionCategory, 'levelCounter' => $levelCounter)
        );
    }

    /**
     * @Route("/category/edit/{id}")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id) {
        return $this->redirect($this->generateUrl('oat_oat_questioncategory_show', array('id' => $id)));
    }
    
    /**
     * @Route("/category/group/edit/{id}")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editGroupAction($id) {
        return $this->redirect($this->generateUrl('oat_oat_questioncategory_showgroup', array('id' => $id)));
    }
    
    /**
     * @Route("/category/group/delete/{id}")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteGroupAction($id) {
        return $this->redirect($this->generateUrl('oat_oat_questioncategory_grouplist'));
    }

    /**
     * @Route("/category/delete/{id}")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $questionCategory = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->find($id);

        if (!$questionCategory) {
            throw $this->createNotFoundException('Question Category with id: ' . $id . ' not found!');
        }

        // Delete all the assessments that make use of this category
        // Delete all categoryAssessments of given category to prevent constrair errors
        $cassQuery = $em->createQuery("DELETE OAT\OATBundle\Entity\CategoryAssessment ca WHERE ca.category = :cat");
        $cassQuery->setParameter('cat', $questionCategory);
        $cassQuery->execute();

        // Delete assessments
        $assQuery = $em->createQuery("SELECT a FROM OAT\OATBundle\Entity\Assessment a JOIN a.questionCategories ca WHERE ca.id = :cat");
        $assQuery->setParameter('cat', $questionCategory);
        $assessments = $assQuery->getResult();
        foreach ($assessments as $assessment) {
            $em->remove($assessment);
        }
        $em->flush();

        $query = $em->createQuery('DELETE OAT\OATBundle\Entity\QuestionCategoryGroupMember qcgm WHERE qcgm.questionCategory = :id');
        $query->setParameter('id', $id);
        $query->execute();
        $em->remove($questionCategory);
        $em->flush();

        return $this->redirect($this->generateUrl('oat_oat_questioncategory_list'));
    }

    /**
     * @Route("/category/group/{groupID}/add/{catID}/{level}")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function addToGroupAction($catID, $groupID, $level) {
        
        if($catID == 0 && $this->getRequest()->getMethod() == 'POST'){
            $catID = $this->getRequest()->request->get('category');
        }
        
        if($level == 0 && $this->getRequest()->getMethod() == 'POST'){
            $level = $this->getRequest()->request->get('level');
        }
        
        $questionCategory = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->find($catID);

        $categoryGroup = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategoryGroup')
                ->find($groupID);

        $em = $this->getDoctrine()->getEntityManager();

        if (!$questionCategory || !$categoryGroup) {
            throw $this->createNotFoundException('Question category or category group with given IDs not found!');
        }

        foreach ($categoryGroup->getQuestionCategoryGroupMember() as $groupMember) {
            if ($groupMember->getQuestionCategory()->getId() == $catID) {
                $groupMember->setTargetScore($level);
                $em->persist($groupMember);
                $em->flush();
                return $this->redirect($this->generateUrl('oat_oat_questioncategory_showgroup', array('id' => $groupID)));
                throw $this->createNotFoundException('This category is already part of the group but the level got updated. Dude, this is the wrong exception!');
            }
        }

        $groupMember = new QuestionCategoryGroupMember();
        $groupMember->setTargetScore($level);
        $groupMember->setQuestionCategory($questionCategory);
        $groupMember->setQuestionCategoryGroup($categoryGroup);

        $em->persist($groupMember);
        $em->flush();
        
        return $this->redirect($this->generateUrl('oat_oat_questioncategory_showgroup', array('id' => $groupID)));
    }
    
    /**
     * @Route("/category/group/{groupID}/remove/{catID}")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function removeFromGroupAction($catID, $groupID) {
        $questionCategory = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->find($catID);

        $categoryGroup = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategoryGroup')
                ->find($groupID);

        $em = $this->getDoctrine()->getEntityManager();

        if (!$questionCategory || !$categoryGroup) {
            throw $this->createNotFoundException('Question category or category group with given IDs not found!');
        }

        foreach ($categoryGroup->getQuestionCategoryGroupMember() as $groupMember) {
            if ($groupMember->getQuestionCategory()->getId() == $catID) {
                $em->remove($groupMember);
                $em->flush();
                return $this->redirect($this->generateUrl('oat_oat_questioncategory_showgroup', array('id' => $groupID)));
            }
        }
        
        throw $this->createNotFoundException('This category isn\'t part of the group so it can\'t be removed');
    }

    /**
     * @Route("/category/group/{id}")
     * @Secure(roles="ROLE_LECTOR")
     */
    public function showGroupAction($id) {
        $categoryGroup = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategoryGroup')
                ->find($id);
        
        if (!$categoryGroup) {
            throw $this->createNotFoundException('Question category or category group with given IDs not found!');
        }
        
        $categories = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->findAll();
        
        return $this->render('OATBundle:CategoryGroup:show.html.twig', array('categoryGroup' => $categoryGroup, 'categories' => $categories));
    }
    
    /**
     * @Route("/category/{id}/print/{level}", defaults={"level" = 0})
     * @Secure(roles="ROLE_LECTOR")
     */
    public function printAction($id, $level) {
    $questionCategory = $this->getDoctrine()
                ->getRepository('OATBundle:QuestionCategory')
                ->find($id);
    
    if (!$questionCategory) {
            throw $this->createNotFoundException('Question category not found!');
        }
    
        $questions = array();
        
        if($level > 0){
             foreach($questionCategory->getQuestions() as $question){
                 if($question->getLevel() == $level){
                     $questions[] = $question;
                 }
             }
        }else{
            $questions = $questionCategory->getQuestions();
        }
    
        return $this->render('OATBundle:QuestionCategory:print.html.twig', array('category' => $questionCategory,'questions' => $questions));
    }
}