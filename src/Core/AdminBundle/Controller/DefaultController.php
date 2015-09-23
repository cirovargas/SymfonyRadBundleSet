<?php

namespace Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        
        $this->get('session')->set('loginUserId', 'oooooooooooi');
//        $em = $this->getDoctrine()->getManager();
//        
//        $data = new \DateTime();
//        $atual = $data->sub(new \DateInterval('P1D'));
//        
//        $qCount = $em->createQueryBuilder();
//        $qCount = $qCount->select('count(e.id)')
//                ->from('ComunicacaoNewsletterBundle:Emails','e')
//                ->where('e.dataEnviado > :atual')
//                ->andWhere('e.dataEnviado is not null')
//                ->setParameter('atual',$atual);
//        
//        $count = $qCount->getQuery()->getSingleScalarResult();
//        
//        echo $count;
        
        return $this->render('CoreAdminBundle::dashboard.html.twig');
    }
}
