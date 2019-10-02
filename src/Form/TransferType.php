<?php

namespace App\Form;

use App\Model\TransferModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TransferType extends AbstractType
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
                'token',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'required' => true,
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
                'data_class'      => TransferModel::class,
                'csrf_protection' => false,
            ]
        );
    }
}
