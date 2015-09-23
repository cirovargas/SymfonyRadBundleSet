<?php

namespace Core\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\UserBundle\Form\SincronizarGruposType;
use Symfony\Component\HttpFoundation\Request;
use Core\UserBundle\Entity\Group;

class DefaultController extends Controller
{
    public function groupsSincronizarLdapAction(Request $request)
    {
        $adldap = $this->get('ad_ldap');
        $em = $this->getDoctrine()->getManager();
        $gruposDB = $em->getRepository('CoreUserBundle:Group')->findAll();
        $gruposBase = $gruposSincronizados = $gruposExcluir = $gruposLDAP = array();
        
        foreach($gruposDB as $gDB){
            if($gDB->getLdapCn() == null || trim($gDB->getLdapCn()) == ''){
                $gruposBase[] = $gDB;
            } else {
                $info = $adldap->group()->info($gDB->getLdapCn());
                if($info['count'] == 0){
                    $gruposExcluir[] = $gDB;
                } else {
                    $gruposSincronizados[] = $gDB;
                }
            }
        }
        
        foreach($adldap->group()->all() as $gLDAP){
            $novo = true;
            $info = $adldap->group()->info($gLDAP);
            if($info['count'] > 0){
                foreach($gruposDB as $gDB){
                    if(trim($gDB->getLdapCn()) == trim($info[0]['cn'][0]))
                        $novo = false;
                }
                if($novo == true){
                    $gruposLDAP[$info[0]['cn'][0]] = !isset($info[0]['description'][0]) ? $info[0]['cn'][0] : $info[0]['description'][0];
                }
            }
        }
        
        $form = $this->createForm(
            new SincronizarGruposType(
                array(
                'gruposBase'=>$gruposBase,
                'gruposExcluir'=>$gruposExcluir,
                'gruposLDAP'=>$gruposLDAP
                )
            )
        );
        
        if($request->getMethod() == 'POST'){
            
                $form->bind($request);
                if ($form->isValid()){
                    $data = $form->getData();

                    foreach($data['gruposBase'] as $grupo){
                        $ldapCN = "GIntranet".str_replace(' ','',ucwords(strtolower($grupo->getName())));
                        try {
                            $attributes = array(
                                "group_name"=> $ldapCN,
                                "description"=>$grupo->getName(),
                                "container"=>array("Groups"),
                            );
                            $result = $adldap->group()->create($attributes);
                            if($result){
                                $grupo->setLdapCn($ldapCN);
                                $em->persist($grupo);
                            }
                        } catch (\Exception $exc) {
                            $this->get('session')->getFlashBag()->add('warning', sprintf('Não foi possível incluir o grupo %s ao LDAP.',$grupo->getName()));
                        }
                    }

                    foreach($data['gruposExcluir'] as $grupo){
                        $em->remove($grupo);
                    }

                    foreach($data['gruposLDAP'] as $grupo){
                        $info = $adldap->group()->info($grupo);
                        $grupoEntity = new Group(!isset($info[0]['description'][0]) ? $info[0]['cn'][0] : $info[0]['description'][0]);
                        $grupoEntity->setLdapCn($info[0]['cn'][0]);
                        $em->persist($grupoEntity);
                    }
                }
                $em->flush();
                $this->get('session')->getFlashBag()->add('success', 'Grupos sincronizados com sucesso');
                return $this->redirect($this->generateUrl('fos_user_group_list'));
            
        }
        
        return $this->render('CoreUserBundle:Default:sincronizar-grupos.html.twig',array(
            'gruposSincronizados' => $gruposSincronizados,
            'form' => $form->createView()
        ));
    }
}
