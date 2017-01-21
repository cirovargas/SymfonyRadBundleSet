<?php

namespace Core\Bundle\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as FormFilterTypes;

class UserFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', FormFilterTypes\BooleanFilterType::class,array(
                'translation_domain' => 'EntityTranslations',
                'label' => 'Ativo'
            ))
            ->add('username', FormFilterTypes\TextFilterType::class,array(
                'label' => 'Usuário',
                'condition_pattern' => FilterOperands::STRING_CONTAINS
            ))
                ->add('email', FormFilterTypes\TextFilterType::class,array(
                'label' => 'E-mail',
                'condition_pattern' => FilterOperands::STRING_CONTAINS
            ))
        ;

        $listener = function(FormEvent $event)
        {
            // Is data empty?
            foreach ($event->getData() as $data) {
                if(is_array($data)) {
                    foreach ($data as $subData) {
                        if(!empty($subData)) return;
                    }
                }
                else {
                    if(!empty($data)) return;
                }
            }

            $event->getForm()->addError(new FormError('Filter empty'));
        };
        $builder->addEventListener(FormEvents::POST_SUBMIT, $listener);
    }

    public function getName()
    {
        return 'Core_userbundle_userfiltertype';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }
}
