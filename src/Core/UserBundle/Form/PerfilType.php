<?php

namespace ADMIN\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PerfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name',null,array('required'=>true, 'label'=>'Nome'))
                ->add('empresa',null,array('label'=>'Empresa (opcional)'))
                ->add('atividadeEmpresa',null,array('label' => 'Atividade da empresa ou Pessoa Física'))
                ->add('telefone',null,array('label'=>'Telefone (fixo)'))
                ->add('celular',null,array('label'=>'Telefone (celular)'))
                ->add('website')
                ->add('facebook')
                ->add('youtube')
                ->remove('username')
                ->add('avatar',new \UTIL\UploadBundle\Form\UploadsType(array('label' => 'Selecione uma imagem ')))
                ->remove('plainPassword')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ADMIN\UserBundle\Entity\User'
        ));
    }
    
    public function getParent()
    {
        return 'fos_user_profile';
    }

    public function getName()
    {
        return 'admin_userbundle_perfil';
    }
}
