<?php

namespace JordiLlonch\Bundle\CrudGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    public function indexAction()
    {
        
        return $this->render('JordiLlonchCrudGeneratorBundle:Default:dashboard.html.twig');
    }
}
