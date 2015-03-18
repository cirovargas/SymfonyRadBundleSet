<?php

namespace Project\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ProjectAdminBundle:Default:index.html.twig');
    }
}
