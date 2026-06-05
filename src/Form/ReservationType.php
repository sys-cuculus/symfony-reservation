<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numberOfPeople', IntegerType::class, [
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter number of people',
                    ),
                    new Positive(
                        message: 'Please enter a valid number of people',
                    )
                ],
            ])
            ->add('dateAndTime', DateTimeType::class, [
                'date_widget' => 'single_text',
                'time_widget' => 'choice',
                'minutes' => [0, 30],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter date and time of reservation',
                    ),
                    new GreaterThan(
                        value: 'now',
                        message: 'The reservation date and time must be in the future',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
