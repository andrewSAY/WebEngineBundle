<?php

namespace WebSite\WebEngineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WebEngineBundle:Default:main.html.twig', array());
    }

    public function pageShowAction($page)
    {
        return $this->render('WebEngineBundle:Default:page.html.twig', array('page' => $page));
    }
}
