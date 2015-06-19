<?php

namespace Core\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use Core\UserBundle\Entity\User;
use Core\UserBundle\Form\UserType;
use Core\UserBundle\Form\EditUserType;
use Core\UserBundle\Form\UserFilterType;
use Core\UserBundle\Form\UserPermissionsType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     */
    public function indexAction()
    {
        list($filterForm, $queryBuilder) = $this->filter();

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return $this->render('CoreUserBundle:User:index.html.twig', array(
            'entities' => $entities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        ));
    }
    
    public function pendentesAction()
    {
        list($filterForm, $queryBuilder) = $this->filter('pendentes');

        list($entities, $pagerHtml) = $this->paginator($queryBuilder);

        return $this->render('CoreUserBundle:User:index.html.twig', array(
            'entities' => $entities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        ));
    }

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($list = '')
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        $filterForm = $this->createForm(new UserFilterType());
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('CoreUserBundle:User')->createQueryBuilder('e');
        if($list == 'pendentes'){
            $queryBuilder->andWhere('e.enabled = false or e.enabled is null');
            $queryBuilder->andWhere('e.lastLogin is null');
        } else {
            //$queryBuilder->andWhere('e.enabled = true or e.enabled is not null');
        }
        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('UserControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->bind($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('UserControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('UserControllerFilter')) {
                $filterData = $session->get('UserControllerFilter');
                $filterForm = $this->createForm(new UserFilterType(), $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder)
    {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPage = $this->getRequest()->get('page', 1);
        $pagerfanta->setCurrentPage($currentPage);
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me)
        {
            return $me->generateUrl('admin_users', array('page' => $page));
        };

        // Paginator - view
        $translator = $this->get('translator');
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => $translator->trans('views.index.pagprev', array(), 'JordiLlonchCrudGeneratorBundle'),
            'next_message' => $translator->trans('views.index.pagnext', array(), 'JordiLlonchCrudGeneratorBundle'),
        ));

        return array($entities, $pagerHtml);
    }

    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $entity  = $userManager->createUser();
        $roles = $this->get('security.system_roles')->getRoles();
        $form = $this->createForm(new UserType(array('roles'=>$roles)), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $password = md5(uniqid());
            $entity->setPlainPassword($password);
            $userManager->updateUser($entity, false);
            
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');
            
            $message = \Swift_Message::newInstance()
                ->setSubject('Registro no sistema')
                ->setFrom('naoresponda@cofen.gov.br')
                ->setTo($entity->getEmail())
                ->setBody(
                    $this->renderView(
                        'CoreUserBundle:Mail:welcome.html.twig',
                        array(
                            'name' => $entity->getName(),
                            'username' => $entity->getUsername(),
                            'password' => $password
                        )
                    ), 'text/html'
                )
            ;
            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl('admin_users_show', array('id' => $entity->getId())));
        }

        return $this->render('CoreUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $entity  = $userManager->createUser();
        $roles = $this->get('security.system_roles')->getRoles();
        $form = $this->createForm(new UserType(array('roles'=>$roles)), $entity);

        return $this->render('CoreUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return $this->render('FOSUserBundle:Profile:show.html.twig', array(
            'user'      => $entity
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $roles = $this->get('security.system_roles')->getRoles();
        $editForm = $this->createForm(new EditUserType(array('roles'=>$roles)), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CoreUserBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $roles = $this->get('security.system_roles')->getRoles();
        $editForm = $this->createForm(new EditUserType(array('roles'=>$roles)), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($entity, false);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 
                $this->get('translator')->trans('flash.update.success',array(),'JordiLlonchCrudGeneratorBundle')
            );

            return $this->redirect($this->generateUrl('admin_users_edit', array('id' => $id)));
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
        }

        return $this->render('CoreUserBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CoreUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'flash.delete.success');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'flash.delete.error');
        }

        return $this->redirect($this->generateUrl('admin_users'));
    }
    
    public function permissionsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);
        $permissionsForm = $this->createForm(new UserPermissionsType(array('roles'=> $this->get('security.system_roles')->getRolesArray())), $entity);
        
        if($request->getMethod() == 'POST'){
            $permissionsForm->bind($request);
            if($permissionsForm->isValid()){
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($entity,false);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'flash.update.success');
            } else {
                $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
            }
        }
        
        return $this->render('CoreUserBundle:User:permissions.html.twig', array(
            'entity'      => $entity,
            'form' => $permissionsForm->createView(),
            'permissions' => $this->get('security.system_roles')->getRoles()
        ));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    
}
