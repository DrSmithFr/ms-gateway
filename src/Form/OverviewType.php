<?php

namespace App\Form;

use App\Entity\Overview;
use App\Enum\EventEnum;
use ReflectionException;
use App\Enum\FeelingEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OverviewType extends AbstractType
{
    /**
     * $option parameter is mandatory but not used
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @throws ReflectionException
     * @param array                $options
     * @param FormBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'mood',
                IntegerType::class,
                [
                    'required'    => true,
                    'constraints' => [
                        new Assert\GreaterThanOrEqual(['value' => 0]),
                        new Assert\LessThanOrEqual(['value' => 10]),
                    ],
                ]
            )
            ->add(
                'feelings',
                ChoiceType::class,
                [
                    'multiple' => true,
                    'choices'  => [
                        FeelingEnum::getAll(),
                    ],
                ]
            )
            ->add(
                'events',
                ChoiceType::class,
                [
                    'multiple' => true,
                    'choices'  => [
                        EventEnum::getAll(),
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
