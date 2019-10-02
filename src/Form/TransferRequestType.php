<?php

namespace App\Form;

use App\Model\PasswordModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class TransferRequestType extends AbstractType
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
                'target',
                EntityType::class,
                [
                    'required' => true,

                ]
            )
            ->add(
                'target_password',
                PasswordType::class,
                [
                    'required' => true
                ]
            )
            ->add(
                'transfer_password',
                PasswordType::class,
                [
                    'required' => true
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
                'data_class'      => PasswordModel::class,
                'csrf_protection' => false,
            ]
        );
    }
}
