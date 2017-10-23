<?php

namespace Loevgaard\PakkelabelsBundle\Form;

use Loevgaard\PakkelabelsBundle\Entity\ShippingMethodMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShippingMethodMappingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source', null, [
                'label' => 'shipping_method_mapping.label.source',
            ])
            ->add('productCode', null, [
                'label' => 'shipping_method_mapping.label.product_code',
            ])
            ->add('serviceCodes', TextType::class, [
                'label' => 'shipping_method_mapping.label.service_codes',
            ])
            ->add('save', SubmitType::class, [
                'label' => 'layout.save',
            ])
        ;

        $builder->get('serviceCodes')
            ->addModelTransformer(new CallbackTransformer(
                function ($serviceCodesAsArray) {
                    return join(', ', $serviceCodesAsArray);
                },
                function ($serviceCodesAsString) {
                    return preg_split('/[,; ]+/i', $serviceCodesAsString, null, PREG_SPLIT_NO_EMPTY);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShippingMethodMapping::class,
        ]);
    }
}
