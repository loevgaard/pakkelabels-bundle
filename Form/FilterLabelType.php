<?php
namespace Loevgaard\PakkelabelsBundle\Form;

use Loevgaard\PakkelabelsBundle\Entity\Label;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class FilterLabelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderId', Filters\NumberFilterType::class, [
                'label' => 'label.filter.order_id'
            ])
            ->add('status', Filters\ChoiceFilterType::class, [
                'label' => 'label.filter.status',
                'choices' => Label::getStatuses()
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'layout.filter'
            ])
            ->setMethod('get')
        ;
    }

    public function getBlockPrefix()
    {
        return 'label_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'LoevgaardPakkelabelsBundle',
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}