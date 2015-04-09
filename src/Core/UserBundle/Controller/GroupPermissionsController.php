<?php

namespace Core\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Core\UserBundle\Form\GroupPermissionsType;

class GroupPermissionsController extends Controller
{

    public function indexAction(Request $request, $groupName)
    {
        $em = $this->getDoctrine()->getManager();

        $groupManager = $this->get('fos_user.group_manager');
        $entity = $groupManager->findGroupBy(array('name' => $groupName));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }
        
        $roles = $this->get('security.system_roles')->getRolesArray();
        
        $editForm = $this->createForm(new GroupPermissionsType(array('roles'=>$roles)), $entity);
        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);

            if ($editForm->isValid()) {
                
                $groupManager->updateGroup($entity, true);
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'flash.update.success');

                return $this->redirect($this->generateUrl('admin_user_group_permissions', array('groupName' => $groupName)));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'flash.update.error');
            }
        }
        return $this->render('CoreUserBundle:Group:permissions.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'permissions' => $this->get('security.system_roles')->getRoles()
        ));
    }
    
}
