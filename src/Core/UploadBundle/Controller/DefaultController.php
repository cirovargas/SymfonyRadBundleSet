<?php

namespace Core\UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CoreUploadBundle:Default:index.html.twig', array('name' => $name));
    }
}
