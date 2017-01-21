<?php

namespace Core\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
   
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
 //               ->add('name')
                ->add('enabled',null,array('label'=>'Ativo'))
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
                ->add('profile',\Core\Bundle\UserBundle\Form\ProfileType::class, array(
                    'label'=> 'Informações básicas'
                ))
//                ->add('plainPassword', 'repeated', array(
//                'type' => 'password',
//                'required' => false,
//                'options' => array('translation_domain' => 'FOSUserBundle'),
//                'first_options' => array('label' => 'form.password'),
//                'second_options' => array('label' => 'form.password_confirmation'),
//                'invalid_message' => 'fos_user.password.mismatch',
//            ))
        ;
        if($options['superadmin'] === true){
            $builder
                ->add('instancias',null,array(
                    //'expanded'=>true,
                    'label'=>'Instâncias',
                    'by_reference' => true,
                    'attr' => array(
                        'class' => 'multiselect'
                    )
                ))
                ->add('ldap',null,array(
                    'label'=>'Servidor de usuários (LDAP)'
                ))
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\UserBundle\Entity\User',
            'superadmin' => false
        ));
    }
    
    public function getParent()
    {
        return \FOS\UserBundle\Form\Type\RegistrationFormType::class;
    }

    public function getName()
    {
        return 'admin_userbundle_user';
    }
}
