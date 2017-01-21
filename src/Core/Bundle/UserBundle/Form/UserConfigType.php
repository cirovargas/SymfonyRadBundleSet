<?php

namespace Core\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserConfigType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('teste',null,array(
//                'required' => false
//            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
//        $resolver->setDefaults(array(
//            'data_class' => 'Core\Bundle\UserBundle\Entity\User'
//        ));
    }
    
    public function getName()
    {
        return 'admin_userbundle_user_configs';
    }
}
