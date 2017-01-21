<?php

namespace Core\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class ProfileFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profile',\Core\Bundle\UserBundle\Form\ProfileType::class, array(
            'label'=> 'Informações básicas'
        ))
            ->add('username',null,array(
                'disabled' => true
            ))
            ->remove('current_password');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\UserBundle\Entity\User',
            'intention'  => 'profile',
        ));
    }
    
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }


    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
