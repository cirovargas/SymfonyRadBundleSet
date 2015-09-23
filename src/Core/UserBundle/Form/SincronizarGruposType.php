<?php

namespace Core\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SincronizarGruposType extends AbstractType
{
    private $gruposLDAP;
    private $gruposExcluir;
    private $gruposBase;
    
    public function __construct($options){
        $this->gruposExcluir = $options['gruposExcluir'];
        $this->gruposBase = $options['gruposBase'];
        $this->gruposLDAP = $options['gruposLDAP'];
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('gruposLDAP','choice',array(
                    'expanded' => false,
                    'attr' => array(
                        'class' => 'multiselect'
                    ),
                    'required' => false,
                    'multiple' => true,
                    'label' => 'Grupos novos no LDAP',
                    'choices' => $this->gruposLDAP
                ))
                ->add('gruposBase','entity',array(
                    'class' => 'CoreUserBundle:Group',
                    'expanded' => false,
                    'attr' => array(
                        'class' => 'multiselect'
                    ),
                    'multiple' => true,
                    'required' => false,
                    'label' => 'Grupos à criar no LDAP',
                    'choices' => $this->gruposBase
                ))
                ->add('gruposExcluir','entity',array(
                    'expanded' => false,
                    'class' => 'CoreUserBundle:Group',
                    'attr' => array(
                        'class' => 'multiselect'
                    ),
                    'label' => 'Grupos excluídos no LDAP',
                    'required' => false,
                    'multiple' => true,
                    'choices' => $this->gruposExcluir
                ))
                
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
           // 'data_class' => 'Core\UserBundle\Entity\User'
        ));
    }
    

    public function getName()
    {
        return 'admin_userbundle_sincronizar_grupos';
    }
}
