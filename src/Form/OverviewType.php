<?php

namespace App\Form;

use App\Entity\Overview;
use App\Enum\OverviewEventEnum;
use App\Enum\OverviewFeelingEnum;
use App\Validator\Constraints\isEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class OverviewType extends AbstractType
{
    /**
     * $option parameter is mandatory but not used
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'mood',
                IntegerType::class,
                [
                    'required'      => true,
                    'property_path' => 'externalId',
                    'constraints'   => [
                        new Assert\GreaterThanOrEqual(['value' => 0]),
                        new Assert\LessThanOrEqual(['value' => 10]),
                    ],
                ]
            )
            ->add(
                'feelings',
                CollectionType::class,
                [
                    'entry_type'  => TextType::class,
                    'constraints' => [
                        new isEnum(),
                    ],
                ]
            )
            ->add(
                'feelings',
                CollectionType::class,
                [
                    'entry_type'  => TextType::class,
                    'constraints' => [
                        new isEnum(['class' => OverviewFeelingEnum::class]),
                    ],
                ]
            )
            ->add(
                'events',
                CollectionType::class,
                [
                    'entry_type'  => TextType::class,
                    'constraints' => [
                        new isEnum(['class' => OverviewEventEnum::class]),
                    ],
                ]
            )
            ->add(
                'note',
                TextType::class,
                );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class'      => Overview::class,
                'csrf_protection' => false,
            ]
        );
    }
}
