<?php

namespace Loevgaard\PakkelabelsBundle\Form;

use Loevgaard\PakkelabelsBundle\Entity\ShippingMethodMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType as CountryTypeField;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', CountryTypeField::class, [
                'label' => 'shipping_method_mapping.label.country',
                'mapped' => false,
                'preferred_choices' => [
                    'DK'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'layout.update',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShippingMethodMapping::class,
            'translation_domain' => 'LoevgaardPakkelabelsBundle'
        ]);
    }
}
