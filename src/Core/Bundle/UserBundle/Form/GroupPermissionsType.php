<?php

namespace Core\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GroupPermissionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('roles',ChoiceType::class,array(
                    'choices'=> array_flip($options['roles']),
                    'expanded'=> false,
                    'required' => false,
                    'multiple' => true
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\UserBundle\Entity\Group',
            'roles' => array()
        ));
    }
    
    public function getName()
    {
        return 'admin_userbundle_grouppermissions';
    }
}
