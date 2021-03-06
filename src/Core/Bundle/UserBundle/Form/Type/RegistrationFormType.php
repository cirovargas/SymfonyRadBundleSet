<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
//    public function __construct($class)
//    {
//        $this->class = $class;
//    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'translation_domain' => 'FOSUserBundle',
                'label_render'        => false,
                'horizontal' => false,
                'attr' => array(
                    'placeholder' => "form.email",
                    'class' => ' input-lg'
                )
            ))
            ->add('username', null, array(
                'label' => 'form.username', 
                'translation_domain' => 'FOSUserBundle',
                'label_render'        => false,
                'horizontal' => false,
                'attr' => array(
                    'placeholder' => "form.username",
                    'class' => ' input-lg'
                )
                ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array(
                    'label' => 'form.password',
                'label_render'        => false,
                'horizontal' => false,
                    'attr' => array(
                        'placeholder' => "form.password",
                        'class' => ' input-lg'
                    )
                    ),
                'second_options' => array(
                    'label' => 'form.password_confirmation',
                    'label_render'        => false,
                    'horizontal' => false,
                    'attr' => array(
                        'placeholder' => "form.password_confirmation",
                        'class' => ' input-lg'
                    )
                    ),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Core\Bundle\UserBundle\Entity\User',
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'core_user_registration';
    }
}
