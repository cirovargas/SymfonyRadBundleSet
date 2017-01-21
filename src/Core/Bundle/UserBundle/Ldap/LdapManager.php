<?php

namespace Core\Bundle\UserBundle\Ldap;

use FR3D\LdapBundle\Ldap\LdapManager as BaseManager;
use Psr\Log\LoggerInterface;
use FR3D\LdapBundle\Hydrator\HydratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LdapManager extends BaseManager{
    
    private $logger;
    private $router;
    private $em;
    
    public function __construct(LoggerInterface $logger, HydratorInterface $hydrator, $router, $doctrine, array $params)
    {
        $this->logger = $logger ;
        $this->hydrator = $hydrator;
        $this->router = $router;
        $this->em = $doctrine->getManager();
        $this->params = $params;
    }
    
    private function createDriver($criteria){
        
        if(is_array($criteria) && isset($criteria[$this->params['usernameAttribute']]) && trim($criteria[$this->params['usernameAttribute']]) != ''){
            $user = $this->em->getRepository('CoreUserBundle:User')->findOneBy(array(
                'username' => $criteria[$this->params['usernameAttribute']],
                'enabled' => 1
            ));
        }
        
        if($criteria instanceof  UserInterface && $criteria->getId() != null){
            $user = $criteria;
        }
        
        if(isset($user) && $user->getLdap() == null)
            return;
        
        if(isset($user) && $user->getLdap() != null){
            $ldap = new \Zend\Ldap\Ldap(array(
                'host' => $user->getLdap()->getConfigs()['ldap_servidor'],
                'username' => $user->getLdap()->getConfigs()['ldap_usuario'],
                'password' => $user->getLdap()->getConfigs()['ldap_senha'],
                'accountDomainName' => $user->getLdap()->getConfigs()['ldap_dominio']
            ));
            $this->driver = new \FR3D\LdapBundle\Driver\ZendLdapDriver($ldap, $this->logger);
            $this->params['baseDn'] = $user->getLdap()->getConfigs()['ldap_basedn'];
            $this->params['filter'] = $user->getLdap()->getConfigs()['ldap_filterusuario'];
            return;
        }
        
        $instancia = $this->em->getRepository('CoreInstanciaBundle:Instancia')->findOneByDominio(
            $this->router->getContext()->getHost()
        );
        
        if(is_array($criteria) && isset($criteria[$this->params['usernameAttribute']]) && trim($criteria[$this->params['usernameAttribute']]) != ''){
            $user = $this->hydrator->getUserManager()->findUserByUsername($criteria[$this->params['usernameAttribute']]);
        }
        
        
        if($instancia){
            $ldap = new \Zend\Ldap\Ldap(array(
                'host' => $instancia->getConfigs()['ldap_servidor'],
                'username' => $instancia->getConfigs()['ldap_usuario'],
                'password' => $instancia->getConfigs()['ldap_senha'],
                'accountDomainName' => $instancia->getConfigs()['ldap_dominio']
            ));
            ;
            //var_dump($ldap->bind('jonatas.sousa','tait0611'));exit;
            $this->driver = new \FR3D\LdapBundle\Driver\ZendLdapDriver($ldap, $this->logger);
            $this->params['baseDn'] = $instancia->getConfigs()['ldap_basedn'];
            $this->params['filter'] = $instancia->getConfigs()['ldap_filterusuario'];
            return;
        }
        
        //throw new \Exception('Não foi possível identificar um servidor LDAP elegível.');
    }
    
    public function findUserBy(array $criteria)
    {
        $this->createDriver($criteria);
        $filter = $this->buildFilter($criteria);
        //echo $filter;exit;

        if(!$this->driver){
            return null;
        }


        $entries = $this->driver->search($this->params['baseDn'], $filter);
        if ($entries['count'] > 1) {
            throw new \Exception('This search can only return a single user');
        }
        if ($entries['count'] == 0) {
            return null;
        }
        
        $user = $this->hydrator->hydrate($entries[0]);
        
        $instancia = $this->em->getRepository('CoreInstanciaBundle:Instancia')->findOneByDominio(
            $this->router->getContext()->getHost()
        );
        
        $user
            ->setLdap($instancia)
            ->addInstancia($instancia)
            ->getProfile()->setNomeCompleto($user->getUsername())
        ;

        $grupos = $this->em->getRepository('CoreUserBundle:Group')->findBy(array(
            'instancia' => $instancia->getId(),
            'padraoLdap' => 1
        ));

        foreach($grupos as $grupo){
            $grupo->addUser($user);
            $user->addGroup($grupo);
        }



        if($user->getEmail() == null)
            $user->setEmail($user->getUsername().'@'.$this->router->getContext()->getHost());

        return $user;
    }
    
    public function bind(UserInterface $user, $password) {
        $this->createDriver($user);
        if(!$this->driver)
            return;
        $instancia = $this->em->getRepository('CoreInstanciaBundle:Instancia')->findOneByDominio(
            $this->router->getContext()->getHost()
        );

        return parent::bind($user, $password);
    }
    
}
