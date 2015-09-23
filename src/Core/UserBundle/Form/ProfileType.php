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
            ->add('name',null,array('label' => 'Nome'))
            ->add('surnames',null,array('label' => 'Sobrenomes'))
            ->add('phone',null,array('label' => 'Telefone'))
            ->add('cellphone',null,array('label' => 'Celular'))
            ->add('workphone',null,array('label' => 'Ramal'))
            //->add('avatar', new \Core\UploadBundle\Form\UploadType())
            ->add('configs', new UserConfigType())
            ->add('bornDate',null,array(
                'format' => 'ddMMyyyy',
                'years' => range(1940,(date('Y')-16)),
                'label' => 'Data de Nascimento'
            ))
            ->add('gender','choice',array(
                'choices' => array(
                    'M' => 'Masculino',
                    'F' => 'Feminino'
                ),
                'label' => 'Sexo'
            ))
            ->add('about','textarea',array('label' =>'Sobre'))
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
