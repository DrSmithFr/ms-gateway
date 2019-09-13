<?php

namespace App\Form;

use App\Model\ConnectionModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
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
                'uuid',
                TextType::class,
                [
                    'required'      => true,
                    'property_path' => 'externalId',
                ]
            )
            ->add(
                'password',
                TextType::class,
                [
                    'required'      => true,
                    'property_path' => 'plainPassword',
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class'      => ConnectionModel::class,
                'csrf_protection' => false,
            ]
        );
    }
}
