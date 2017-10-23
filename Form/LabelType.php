<?php

namespace Loevgaard\PakkelabelsBundle\Form;

use Loevgaard\PakkelabelsBundle\Entity\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LabelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // @todo fix this form
        /*$builder
            ->add('source', null, [
                'label' => 'country_mapping.label.source'
            ])
            ->add('countryCode', null, [
                'label' => 'country_mapping.label.country_code'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'layout.save'
            ])
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Label::class,
        ]);
    }
}
