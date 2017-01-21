<?php

namespace Core\Bundle\AdminBundle\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdater;
use Symfony\Component\Form\FormFactory;

/**
 * Description of ListFilterService
 *
 * @author ciro.marques
 */
class ListFilterService {
    
    private $request;
    private $requestStack;
    private $em;
    private $filterUpdater;
    private $formFactory;
    private $translator;
    
    public function __construct(
            RequestStack $requestStack,
            EntityManager $em,
            FilterBuilderUpdater $filterUpdater,
            FormFactory $formFactory,
             $translator
    ){
        $this->requestStack = $requestStack;
        $this->request = $this->requestStack->getMasterRequest();
        $this->em = $em;
        $this->filterUpdater = $filterUpdater;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }
    
    
    public function filter($filterType,$entityString,$qbCallback = null,$sessionKey = '',$filterButton = null,$resetButton = null,$formParams = array())
    {
        $session = $this->request->getSession();
        $filterForm = $this->formFactory->create($filterType,null,$formParams);
        $queryBuilder = $this->em->getRepository($entityString)->createQueryBuilder('e');
        if(is_callable($qbCallback)){
            $queryBuilder = $qbCallback($queryBuilder);
        }
        
        
        $filterButton = $filterButton ? $filterButton : $this->translator->trans('views.index.filter', array(), 'CoreAdminBundle');
        $resetButton = $resetButton ? $resetButton : $this->translator->trans('views.index.reset', array(), 'CoreAdminBundle');
        
        if(trim($sessionKey) === ''){
            $sessionKey = preg_replace('~![a-zA-Z0-9]~', '', $entityString).'FilterForm';
        }

        // Reset filter
        if ($this->request->get('filter_action') == $resetButton) {
            $session->remove($sessionKey);
        }
        
        // Filter action
        if ($this->request->get('filter_action') == $filterButton) {
            // Bind values from the request

            if( in_array($this->request->getMethod(),array('POST','PUT','PATCH'))){
                $filterForm->handleRequest($this->request);
            } else {
                $filterForm->submit($this->request->get($filterForm->getName()));
            }
            
            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->filterUpdater->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set($sessionKey, $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has($sessionKey)) {
                $filterData = $session->get($sessionKey);
                $filterForm = $this->formFactory->create($filterType, $filterData);
                $this->filterUpdater->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }
    
    
}
