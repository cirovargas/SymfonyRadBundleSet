<?php

namespace Project\ExternalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProjectExternalBundle:Default:index.html.twig');
    }
}
