<?php

namespace Core\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditUserType extends AbstractType
{
   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('groups',null,array('expanded'=>true))
 //               ->add('name')
                ->add('enabled')
                //->add('roles','choice_tree',array(
                //    'choices'=> $this->roles, 
                //    'expanded'=> true,
               //     'multiple' => true
              //  ))
  //              ->add('empresa',null,array('label'=>'Empresa (Opcional)'))
      //          ->add('atividadeEmpresa')
       //         ->add('telefone')
        //        ->add('website')
          //      ->add('facebook')
            //    ->add('youtube')
 //               ->add('avatar',new \UTIL\UploadBundle\Form\UploadsType(array('label' => 'Avatar')))
                ->remove('plainPassword')
                ->add('profile',new \Core\UserBundle\Form\ProfileType());
//                ->add('plainPassword', 'repeated', array(
//                'type' => 'password',
//                'required' => false,
//                'options' => array('translation_domain' => 'FOSUserBundle'),
//                'first_options' => array('label' => 'form.password'),
//                'second_options' => array('label' => 'form.password_confirmation'),
//                'invalid_message' => 'fos_user.password.mismatch',
//            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User'
        ));
    }
    
    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'admin_userbundle_user';
    }
}
