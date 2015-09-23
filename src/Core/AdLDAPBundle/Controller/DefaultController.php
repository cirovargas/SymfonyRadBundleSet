<?php

namespace Core\AdLDAPBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CoreAdLDAPBundle:Default:index.html.twig', array('name' => $name));
    }
}
