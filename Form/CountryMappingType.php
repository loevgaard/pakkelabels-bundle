<?php
namespace Loevgaard\DandomainAltapayBundle\Form;

use Loevgaard\PakkelabelsBundle\Entity\CountryMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryMappingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', null, [
                'label' => 'country_mapping.label.source'
            ])
            ->add('countryCode', null, [
                'label' => 'country_mapping.label.country_code'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'layout.save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CountryMapping::class,
        ));
    }
}