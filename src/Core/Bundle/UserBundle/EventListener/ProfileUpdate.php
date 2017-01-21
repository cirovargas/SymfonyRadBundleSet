<?php
/**
 * Created by PhpStorm.
 * User: ciro
 * Date: 13/06/16
 * Time: 21:18
 */

namespace Core\Bundle\UserBundle\EventListener;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;

class ProfileUpdate implements EventSubscriberInterface
{
    protected $userManager;
    protected $router;

    public function __construct(UserManagerInterface $userManager, $router)
    {
        $this->userManager = $userManager;
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return array(
            //FOSUserEvents::PROFILE_EDIT_SUCCESS => 'uploadAvatar',
            //SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'redirecionarInstancia'
        );
    }

    public function uploadAvatar(FormEvent $event)
    {
        $user = $event->getUser();

        $user->setLastLogin(new \DateTime());
        $this->userManager->updateUser($user);
    }
    
    public function redirecionarInstancia(FormEvent $event){
        
        if(null == $event->getRequest()->getSession()->get('instancia')){
            $event->setResponse(new \Symfony\Component\HttpFoundation\RedirectResponse($this->router->generate('core_instancia_homepage')));
        }
        
        
    }

}
