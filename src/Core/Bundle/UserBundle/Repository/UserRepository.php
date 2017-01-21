<?php

namespace Core\Bundle\UserBundle\Repository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    
    public function getUserGroupsInstancia($usuario,$instancia){
        $query = $this->createQueryBuilder('u')
            ->where('u.id = :usuario')
            ->join('u.groups','g','WITH','g.instancia = :instancia')
            ->setParameter('instancia',$instancia)
            ->setParameter('usuario',$usuario)
            ->getQuery();
        
        return $query->getResult();
    }
    
    
}
