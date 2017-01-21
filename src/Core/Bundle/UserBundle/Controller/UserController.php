<?php

namespace Core\Bundle\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;
use Core\Bundle\UserBundle\Entity\User;
use Core\Bundle\UserBundle\Form\UserType;
use Core\Bundle\UserBundle\Form\EditUserType;
use Core\Bundle\UserBundle\Form\UserFilterType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Security("is_granted('ROLE_ADMIN_USERS')")
     */
    public function indexAction(Request $request)
    {
        list($filterForm, $queryBuilder) = $this->filter($request);

        list($entities, $pagerHtml) = $this->paginator($request,$queryBuilder);

        return $this->render('CoreUserBundle:User:index.html.twig', array(
            'entities' => $entities,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
        ));
    }
    
    public function pendentesAction(Request $request)
    {
        list($filterForm, $queryBuilder) = $this->filter($request,'pendentes');

        list($entities, $pagerHtml) = $this->paginator($request,$queryBuilder);

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
    protected function filter($request,$list = '')
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('Core\Bundle\UserBundle\Form\UserFilterType');
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('CoreUserBundle:User')
                ->createQueryBuilder('e');

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
            if( in_array($request->getMethod(),array('POST','PUT','PATCH'))){
                $filterForm->handleRequest($request);
            } else {
                $filterForm->submit($request->get($filterForm->getName()));
            }

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
                $filterForm = $this->createForm('Core\Bundle\UserBundle\Form\UserFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }

    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($request,$queryBuilder)
    {
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $currentPage = $request->get('page', 1);
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
            'prev_message' => $translator->trans('views.index.pagprev', array(), 'CoreAdminBundle'),
            'next_message' => $translator->trans('views.index.pagnext', array(), 'CoreAdminBundle'),
        ));

        return array($entities, $pagerHtml);
    }

    /**
     * Creates a new User entity.
     *
     * @Security("is_granted('ROLE_ADMIN_USERS_ADD')")
     */
    public function createAction(Request $request)
    {
        $userManager = $this->get('fos_user.user_manager');
        $entity  = $userManager->createUser();
        $form = $this->createForm(UserType::class, $entity,array('superadmin'=>$this->getUser()->hasRole('ROLE_SUPERADMIN')));
        $form->handleRequest($request);
        
        if ($form->isSubmitted()) {
            
            $outros = $userManager->findUserByUsername($form->get('username')->getData());
            
            if(count($outros) > 0){
                $form->addError(new \Symfony\Component\Form\FormError('Já existe outro usuário com o mesmo login'));
            }
            
            if ($form->isValid()) {

                try {
                    $em = $this->getDoctrine()->getManager();
                        $entity->setPlainPassword('admin');

                    $userManager->updateUser($entity, true);
                    $this->get('session')->getFlashBag()->add('success', 
                        $this->get('translator')->trans('flash.create.success',array(),'CoreAdminBundle')
                    );

                } catch (\Exception $exc) {
                    $this->get('session')->getFlashBag()->add('danger', 
                        $this->get('translator')->trans('flash.create.error',array(),'CoreAdminBundle')
                    );
                    if($this->getUser()->hasRole('ROLE_SUPERADMIN')){
                        $this->get('session')->getFlashBag()->add('danger', 
                            $exc->getMessage()
                        );
                    }
                    
                } finally {
                    if($entity->getLdap() == null){
                        try {
                            $message = \Swift_Message::newInstance()
                                ->setSubject('Registro no sistema')
                                ->setFrom($this->getParameter('mailer_user'))
                                ->setTo($entity->getEmail())
                                ->setBody(
                                    $this->renderView(
                                        'CoreUserBundle:Mail:welcome.html.twig',
                                        array(
                                            'name' => $entity->getProfile()->getName(),
                                            'username' => $entity->getUsername(),
                                            'password' => $password
                                        )
                                    ), 'text/html'
                                )
                            ;
                            $this->get('mailer')->send($message);
                        } catch (\Exception $exc) {
                            $this->get('session')->getFlashBag()->add(
                                    'danger', 
                                    'Não foi possível enviar a senha por e-mail: '.$password
                            );
                        }
                    }
                    if(trim($entity->getId()) != ''){
                        return $this->redirect($this->generateUrl('admin_users_show', array('id' => $entity->getId())));
                    }
                        
                }
            }
        }

        return $this->render('CoreUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Security("is_granted('ROLE_ADMIN_USERS_ADD')")
     */
    public function newAction()
    {
        $userManager = $this->get('fos_user.user_manager');
        $entity  = $userManager->createUser();
        $form = $this->createForm(UserType::class, $entity,array('superadmin'=>$this->getUser()->hasRole('ROLE_SUPERADMIN')));

        return $this->render('CoreUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     * @Security("is_granted('ROLE_ADMIN_USERS_SHOW')")
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
     * @Security("is_granted('ROLE_ADMIN_USERS_EDIT')")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $editForm = $this->createForm(EditUserType::class, $entity,array('superadmin'=>$this->getUser()->hasRole('ROLE_SUPERADMIN')));
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
     * @Security("is_granted('ROLE_ADMIN_USERS_EDIT')")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $deleteForm = $this->createDeleteForm($id);
        
        $editForm = $this->createForm(EditUserType::class, $entity,array('superadmin'=>$this->getUser()->hasRole('ROLE_SUPERADMIN')));
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                
                if($entity->getLdap() != null){
                    $entity->setPassword('');
                }

                $this->get('fos_user.user_manager')->updateUser($entity, true);

                $this->get('session')->getFlashBag()->add('success', 
                    $this->get('translator')->trans('flash.update.success',array(),'CoreAdminBundle')
                );

                return $this->redirect($this->generateUrl('admin_users_edit', array('id' => $id)));
            } catch (\Exception $exc) {
                $this->get('session')->getFlashBag()->add('danger', 
                    $this->get('translator')->trans('flash.update.error',array(),'CoreAdminBundle')
                );
                $this->get('session')->getFlashBag()->add('danger', 
                    $exc->getMessage()
                );
            }   
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
     * @Security("is_granted('ROLE_ADMIN_USERS_DELETE')")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('CoreUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em->remove($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 
                    $this->get('translator')->trans('flash.delete.success',array(),'CoreAdminBundle')
                );
            } catch (\Exception $exc) {
                $this->get('session')->getFlashBag()->add('danger', 
                    $this->get('translator')->trans('flash.delete.error',array(),'CoreAdminBundle')
                );
                return $this->redirect($this->generateUrl('admin_users_inativar',array('id' =>$id)));
            }
        }

        return $this->redirect($this->generateUrl('admin_users'));
    }
    
    /**
     * Deletes a User entity.
     *
     * @Security("is_granted('ROLE_ADMIN_USERS_ATIVAR')")
     */
    public function ativarUsuarioAction(Request $request, User $user){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $user->setEnabled(true);
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Usuário ativado com sucesso.');
        } catch (\Exception $exc) {
            $this->get('session')->getFlashBag()->add('danger', 'Erro ao ativar o usuário');
        }
        return $this->redirect($this->generateUrl('admin_users'));
    }
    
    /**
     * Deletes a User entity.
     *
     * @Security("is_granted('ROLE_ADMIN_USERS_INATIVAR')")
     */
    public function inativarUsuarioAction(Request $request, User $user){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $user->setEnabled(false);
            $em->persist($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Usuário inativado com sucesso.');
        } catch (\Exception $exc) {
            $this->get('session')->getFlashBag()->add('danger', 'Erro ao inativar o usuário');
        }
        return $this->redirect($this->generateUrl('admin_users'));
        
    }
    
    /**
     * User permissions.
     *
     * @Security("is_granted('ROLE_ADMIN_USERS_PERMISSIONS')")
     */
    public function permissionsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreUserBundle:User')->find($id);
        $form = $this->createForm(
                'Core\Bundle\UserBundle\Form\UserPermissionsType', 
                $entity,
                array('roles'=> $this->get('security.system_roles')->getRolesArray())
            );
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try {
                $userManager = $this->get('fos_user.user_manager');
                $userManager->updateUser($entity,true);
                $this->get('session')->getFlashBag()->add('success', 
                    $this->get('translator')->trans('flash.update.success',array(),'CoreAdminBundle')
                );
            } catch (\Exception $exc) {
                $this->get('session')->getFlashBag()->add('danger', 
                    $this->get('translator')->trans('flash.update.error',array(),'CoreAdminBundle')
                );
            }
        }
        
        return $this->render('CoreUserBundle:User:permissions.html.twig', array(
            'entity'      => $entity,
            'form' => $form->createView(),
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
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_users_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm();
    }

    public function avatarAction(Request $request){

        $form = $this->createForm('Core\Bundle\UserBundle\Form\AvatarType');

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try {
                $em = $this->getDoctrine()->getManager();
                $imagemDestacada = $this->get('core_upload.handler')->handle($form['avatarArquivo']->getData());
                if($imagemDestacada){
                    $profile = $em->getRepository('CoreUserBundle:Profile')->findOneByUser($this->getUser()->getId());
                    $profile
                        ->setAvatar($imagemDestacada)
                    ;
                    $em->persist($profile);
                    $em->persist($imagemDestacada);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('success', 
                    $this->get('translator')->trans('flash.update.success',array(),'CoreAdminBundle')
                );
                }
            } catch (\Exception $exc) {
                $this->get('session')->getFlashBag()->add('danger', 
                    $this->get('translator')->trans('flash.update.error',array(),'CoreAdminBundle')
                );
            }
        }

        return $this->render('CoreUserBundle:User:avatar.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    
}
