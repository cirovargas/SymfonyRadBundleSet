<?php

namespace Core\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    private $roles;
    
    public function __construct($options){
        $this->roles = $options['roles'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('groups',null,array('expanded'=>true))
                ->add('enabled')
//                ->add('avatar',new \UTIL\UploadBundle\Form\UploadsType(array('label' => 'Avatar')))
                ->remove('plainPassword')
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
