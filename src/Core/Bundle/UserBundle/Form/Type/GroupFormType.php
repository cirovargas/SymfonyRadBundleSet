<?php

namespace Core\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The Group class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users',null,array(
                'choices' => $options['users'],
                'attr' => array(
                    'class' => 'multiselect'
                ),
                'by_reference' => false,
                'label' => 'Usuários'
            ))
            ->add('padraoLdap',null,array(
                'label' => 'Anexar aos usuários do  LDAP'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'users' => array(),
            'intention'  => 'group',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function getName()
    {
        return 'core_user_group';
    }
    
    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\GroupFormType';
    }
}
