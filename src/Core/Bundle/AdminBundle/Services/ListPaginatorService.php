<?php

namespace Core\Bundle\AdminBundle\Services;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;


/**
 * Description of ListPaginationService
 *
 * @author ciro.marques
 */
class ListPaginatorService {
    
    private $request;
    private $requestStack;
    private $translator;
    private $router;
    
    public function __construct(RequestStack $requestStack, $translator,Router $router){
        $this->requestStack = $requestStack;
        $this->request = $this->requestStack->getMasterRequest();
        $this->translator = $translator;
        $this->router = $router;
    }
    
    public function paginator($queryBuilder,$route,$maxPerPage = 10,array $params = array())
    {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($maxPerPage);
        $currentPage = $this->request->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $router = $this->router;
        $routeGenerator = function($page) use ($router,$route,$params)
        {
            $params['page'] = $page;
            return $router->generate($route,$params,UrlGeneratorInterface::ABSOLUTE_PATH);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => $this->translator->trans('views.index.pagprev', array(), 'CoreAdminBundle'),
            'next_message' => $this->translator->trans('views.index.pagnext', array(), 'CoreAdminBundle'),
        ));

        return array($entities, $pagerHtml);
    }
    
}
