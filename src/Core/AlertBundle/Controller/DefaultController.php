<?php

namespace Core\AlertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller{
    
    public function containerAction(){
        
        $em = $this->getDoctrine()->getManager();
        
//        $this->get('core.alert')->alertAll('ALMEIDA1','Sua solicitação de produtos foi atendida','http://google.com.br','fa-bell-o');
        //$this->get('core.alert')->alertAll('ALMEIDA2','Sua solicitação de produtos foi atendida','http://google.com.br','fa-bell-o');
        //$this->get('core.alert')->alertAll('Mangueira','Cadê o pad 822','http://google.com.br','fa-bell-o');
        //$em->flush();
        $qb = $em->getRepository('CoreAlertBundle:UserAlert')->createQueryBuilder('u');
        $qb = $qb->innerJoin('u.alert','a')
                ->addSelect('a')
                ->where('u.user = :user')
                ->setParameter('user',$this->getUser()->getId())
                ->groupBy('a.id')->orderBy('a.id','DESC')->setMaxResults(10)->getQuery();
        
        $messages = $qb->getResult();
        
        $qCount = $em->createQueryBuilder();
        $qCount = $qCount->select('count(u.id)')
                ->from('CoreAlertBundle:UserAlert','u')
                ->where('u.readed is null')
                ->andWhere('u.user = :user')
                ->setParameter('user',$this->getUser()->getId());

        $count = $qCount->getQuery()->getSingleScalarResult();
        
        return $this->render('CoreAlertBundle:Default:container.html.twig',array(
            'messages' => $messages,
            'count' => $count
        ));
    }
    
    public function refreshAction(Request $request, $lastId){
       
        $em = $this->getDoctrine()->getManager();
       
        $qb = $em->getRepository('CoreAlertBundle:UserAlert')->createQueryBuilder('u');
        $qb = $qb->innerJoin('u.alert','a')
                ->addSelect('a')
                ->where('u.user = :user')
                ->andWhere('u.id > :last')
                ->setParameter('last',$lastId)
                ->setParameter('user',$this->getUser()->getId())
                ->groupBy('a.id')->orderBy('a.id','DESC')->getQuery();
        
       
       
        $qCount = $em->createQueryBuilder();
        $qCount = $qCount->select('count(u.id)')
                ->from('CoreAlertBundle:UserAlert','u')
                ->where('u.readed is null')
                ->andWhere('u.user = :user')
                ->setParameter('user',$this->getUser()->getId());

        $count = $qCount->getQuery()->getSingleScalarResult();
        
        return new JsonResponse(array(
            'count'=> $count,
            'messages'=> $qb->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
        ));
    }
   
    public function allAction(){
       
        $em = $this->getDoctrine()->getManager();
        
        $qb = $em->getRepository('CoreAlertBundle:UserAlert')->createQueryBuilder('u');
        $qb = $qb->join('u.alert','a')
                ->addSelect('a')
                ->where('u.user = :user')
                ->setParameter('user',$this->getUser()->getId())
                ->groupBy('a.id')->orderBy('a.id','DESC')->setMaxResults(50)->getQuery();
        
        $messages = $qb->getResult();
        
        $data = new \DateTime();
        foreach($messages as $message){
            if($message->getReaded() == false){
                $message
                    ->setReaded(true)
                    ->setDateReaded($data);
                $em->persist($message);
            }
        }
        
        $em->flush();
        
        return $this->render('CoreAlertBundle:Default:all.html.twig',array(
            'messages' => $messages
        ));
    }
   
    public function setReadedAction(Request $request){
       
        
            $em = $this->getDoctrine()->getManager();
            $notifications = $em->getRepository('CoreAlertBundle:UserAlert')->findBy(array('id'=>$request->request->get('notificationIds')));
            $ids = array();
            
            $qCount = $em->createQueryBuilder();
            $qCount = $qCount->select('count(u.id)')
                ->from('CoreAlertBundle:UserAlert','u')
                ->where('u.readed is null')
                ->andWhere('u.user = :user')
                ->setParameter('user',$this->getUser()->getId());

            $count = $qCount->getQuery()->getSingleScalarResult();
            
            if(count($notifications) > 0){
                $data = new \DateTime();
                foreach($notifications as $notification){
                    $ids[] = $notification->getId();
                    $notification
                        ->setReaded(true)
                        ->setDateReaded($data);
                    $em->persist($notification);
                }
                $em->flush();
            }
      
        return new JsonResponse(array('count'=>$count,'ids'=>$ids));
    }
}
