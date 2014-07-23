<?php

namespace OAT\OATBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render('OATBundle:Default:index.html.twig', array());
    }
    
    /**
     * @Route("/feedback")
     * @Template()
     */
    public function feedbackAction()
    {
        return $this->render('OATBundle:Default:feedback.html.twig', array());
    }
}
