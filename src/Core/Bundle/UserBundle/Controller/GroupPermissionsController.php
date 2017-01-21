<?php

namespace Core\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class GroupPermissionsController extends Controller
{

    /**
     * User permissions.
     *
     * @Security("is_granted('ROLE_ADMIN_USERGROUPS_PERMISSIONS')")
     */
    public function indexAction(Request $request, $groupName)
    {
        $groupManager = $this->get('fos_user.group_manager');
        $entity = $groupManager->findGroupBy(array('id' => $groupName));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }
        
        $roles = $this->get('security.system_roles')->getRolesArray();
        
        $editForm = $this->createForm(
            'Core\Bundle\UserBundle\Form\GroupPermissionsType', 
            $entity,
            array('roles'=>$roles)
        );
        
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                try {
                    $groupManager->updateGroup($entity, true);
                    $this->get('session')->getFlashBag()->add('success', 
                        $this->get('translator')->trans('flash.update.success',array(),'CoreAdminBundle')
                    );
                    return $this->redirect(
                            $this->generateUrl('admin_user_group_permissions', 
                                    array('groupName' => $groupName)
                                    )
                            );
                    
                } catch (\Exception $exc) {
                    $this->get('session')->getFlashBag()->add('danger', 
                        $this->get('translator')->trans('flash.update.error',array(),'CoreAdminBundle')
                    );
                }   
            }
        return $this->render('CoreUserBundle:Group:permissions.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'permissions' => $this->get('security.system_roles')->getRoles()
        ));
    }
    
}
