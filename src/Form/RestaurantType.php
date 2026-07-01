<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('restaurant_name', options: [
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter restrant name'
                    )
                ]
            ])
            ->add('address', options: [
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter address',
                    ),
                ]
            ])
            ->add('tel', TelType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter tel number',
                    ),
                    new Length(
                        min: 10,
                        minMessage: 'Tel number has to be at least 10 digits',
                    ),
                    new Regex(
                        pattern: '/^\+?[0-9]{10,14}$/',
                        message: 'Please enter a valid number'
                    )
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
