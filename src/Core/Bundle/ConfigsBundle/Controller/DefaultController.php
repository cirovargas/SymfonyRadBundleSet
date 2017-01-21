<?php

namespace Core\Bundle\ConfigsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreConfigsBundle:Default:index.html.twig');
    }
}
