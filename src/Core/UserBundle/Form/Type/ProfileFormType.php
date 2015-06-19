<?php

namespace Core\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profile',new \Core\UserBundle\Form\ProfileType());
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\UserBundle\Entity\User',
            'intention'  => 'profile',
        ));
    }
    
    public function getParent()
    {
        return 'fos_user_profile';
    }


    public function getName()
    {
        return 'core_user_profile';
    }
}
