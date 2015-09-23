<?php

namespace Core\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserPermissionsType extends AbstractType
{
    private $roles;
    
    public function __construct($options){
        $this->roles = $options['roles'];
        
        //print_r($this->roles);
        
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('userRoles','choice',array(
                    'choices'=> $this->roles, 
                    'expanded'=> false,
                    'multiple' => true,
                    'required' => false
                ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User'
        ));
    }
    
    public function getName()
    {
        return 'admin_userbundle_userpermissions';
    }
}
