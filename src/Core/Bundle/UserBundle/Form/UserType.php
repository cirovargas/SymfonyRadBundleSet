<?php

namespace Core\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('enabled',null,array('label'=>'Ativo'))
//                ->add('avatar',new \UTIL\UploadBundle\Form\UploadsType(array('label' => 'Avatar')))
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\UserBundle\Entity\User',
            'superadmin' => false
        ));
    }
    

    public function getName()
    {
        return 'admin_userbundle_user';
    }
}
