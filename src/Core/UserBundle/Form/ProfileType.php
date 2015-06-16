<?php

namespace Core\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('surnames')
            ->add('phone')
            ->add('cellphone')
            ->add('workphone')
            ->add('bornDate')
            ->add('gender','choice',array(
                'choices' => array(
                    'M' => 'Male',
                    'F' => 'Female'
                )
            ))
            ->add('about','textarea')
            //->add('avatar', new \Core\UploadBundle\Form\UploadType())
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\Core\UserBundle\Entity\Profile'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_userbundle_profile';
    }
}
