<?php

namespace Core\Bundle\AlertaBundle\Services;

use Core\Bundle\AlertaBundle\Entity\Alerta;
use Core\Bundle\AlertaBundle\Entity\UserAlerta;
use Doctrine\ORM\EntityManager;

class AlertService {

    private $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function alert($system,$text,$url,$icon,$users = array(),$groups = array(),$level = 'default'){
        
        if(empty($users) && empty($groups)){
            throw new \Exception('You must specify at least one user or group to receive the alert.');
            return;
        }
        if(!empty($groups)){
            $users = $this->getUsersInGroups($groups, $users);
        }
        
        $this->sendAlert($system, $text, $url, $icon, $users,$level);
        
    }
    
    public function alertAll($system,$text,$url,$icon,$level = 'default'){
        
        $users = $this->em->getRepository('CoreUserBundle:User')->findAll();
        
        $this->sendAlert($system, $text, $url, $icon, $users,$level);
        
    }
    
    public function alertGroups($system,$text,$url,$icon,$groups,$level = 'default'){
        if(empty($groups)){
            throw new \Exception('You must specify at least one group to receive the alert.');
            return;
        }
        
        $users = $this->getUsersInGroups($groups);
        $this->sendAlert($system, $text, $url, $icon, $users,$level);
    }
    
    public function alertUsers($system,$text,$url,$icon,$users,$level = 'default'){
        if(empty($users)){
            throw new \Exception('You must specify at least one user to receive the alert.');
            return;
        }
        
        $this->sendAlert($system, $text, $url, $icon, $users,$level);
    }
    
    private function sendAlert($system,$text,$url,$icon,$users,$level){
        
        $alert = new Alerta();
        $alert
            ->setSistema($system)
            ->setData(new \DateTime())
            ->setTexto($text)
            ->setUrl($url)
            ->setLevel($level)
            ->setIcone($icon);
        
        $this->em->persist($alert);
        
        foreach ($users as $user) {
            $userAlert = new UserAlerta();
            $userAlert
                ->setAlert($alert)
                ->setUser($user);
            $this->em->persist($userAlert);
        }
    }
    
    private function getUsersInGroups($groups,$usersToMerge = array()){
        
        $groupsArray = array();
        foreach($groups as $group){
            if(!($group instanceof \Core\UserBundle\Entity\Group)){
                throw new Exception("The group must be instance of '\Core\UserBundle\Entity\Group'");
            } else {
                $groupsArray[] = $group->getId();
            }
        }

        $qGroups = $this->em->getRepository('CoreUserBundle:User')->createQueryBuilder('u');
        $qGroups = $qGroups->innerJoin('u.groups','g','WITH',$qGroups->expr()->in('g.id',$groupsArray))
                ->groupBy('u.id')->getQuery();
        
        $usersInGroups = $qGroups->execute();

        $usersToMerge = array_merge($usersToMerge,$usersInGroups);
        $usersTemp = array();
        foreach($usersToMerge as $user){
            $usersTemp[$user->getId()] = $user;
        }
        $usersToMerge = $usersTemp;
        unset($usersTemp);
        return $usersToMerge;
    }
}
