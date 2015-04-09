<?php

namespace ADMIN\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserRegisterType extends AbstractType
{
    private $roles;
    
    public function __construct(){
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                //->add('groups',null,array('expanded'=>true))
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
                //->add('enabled')
            //    ->add('perfilProfissional',new \PPM\BaseBundle\Form\PerfilProfissionalType())
//                ->add('avatar',new \UTIL\UploadBundle\Form\UploadsType(array('label' => 'Avatar')))
                ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => false,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'Confirmar senha'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
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
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'admin_userbundle_user_register';
    }
}
