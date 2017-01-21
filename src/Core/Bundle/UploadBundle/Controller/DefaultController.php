<?php

namespace Core\Bundle\UploadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\Bundle\UploadBundle\Entity\Upload;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreUploadBundle:Default:index.html.twig');
    }
    
    public function arquivoSigilosoAction(Request $request, Upload $upload){
        $response = new BinaryFileResponse($upload->getFile());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
    
    public function arquivoSigilosoMd5Action(Request $request, Upload $upload){
//        
//        $em = $this->getDoctrine()->getManager();
//        echo $md5;exit;
//        $upload = $em->getRepository('CoreUploadBundle:Upload')->findByMd5($md5);
        
        $response = new BinaryFileResponse($upload->getFile());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}