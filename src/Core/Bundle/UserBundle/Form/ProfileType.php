<?php

namespace Core\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
class ProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array(
                'label' => 'Nome',
                'required' => true
            ))
            ->add('nomeCompleto',null,array('label' => 'Nome completo'))
            ->add('cellphone',null,array(
                'label' => 'Celular',
                'attr' => array(
                    'class' => 'mask-telefone'
                )
            ))
            ->add('phone',null,array(
                'label' => 'Telefone',
                'required' => true,
                'attr' => array(
                    'class' => 'mask-telefone'
                )
                ))
            ->add('workphone',null,array(
                'label' => 'Ramal',
                'required' => true,
                'attr' => array(
                    'class' => 'mask-ramal'
                )
                ))
            //->add('avatar', new \Core\Bundle\UploadBundle\Form\UploadType())
            ->add('configs', UserConfigType::class,array(
                'label' => 'Configurações'
            ))
            ->add('bornDate',  \Symfony\Component\Form\Extension\Core\Type\DateType::class,array(
                'format' => 'ddMMyyyy',
                'years' => range(1940,(date('Y')-16)),
                'label' => 'Data de Nascimento'
            ))
            ->add('gender', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class,array(
                'choices' => array(
                    'Masculino' => 'M',
                    'Feminino' => 'F'
                ),
                'label' => 'Sexo'
            ))
            ->add('about', TextareaType::class,array('label' =>'Sobre'))
            ->add('atividadesDesenvolvidas', TextareaType::class,array('required'=>false, 'label' =>'Descreva sobre suas atividades desenvolvidas no local de trabalho'))
            ->add('aptidao', TextareaType::class,array('required'=>false, 'label' =>'Descreva as atividades que está apto a desenvolver'))
//            ->add('avatarArquivo', FileType::class,array(
//                'mapped' => false,
//                'attr' => array(
//                    'class' => 'input-file'
//                )
//            ))
        ;
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\Core\Bundle\UserBundle\Entity\Profile'
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
