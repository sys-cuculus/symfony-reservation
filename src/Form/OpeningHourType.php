<?php

namespace App\Form;

use App\Entity\OpeningHour;
use App\Entity\Restaurant;
use App\Enum\DayOfWeek;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class OpeningHourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dayOfWeek', EnumType::class, [
                'class' => DayOfWeek::class,
                'constraints' => [
                    new NotBlank(
                        message: 'Please choose a day of week',
                    ),
                ],
            ])
            ->add('openTime', TimeType::class, [
                'error_bubbling' => true,
                'required' => false,
            ])
            ->add('closeTime', TimeType::class, [
                'error_bubbling' => true,
                'required' => false,
            ])
            ->add('closedFlag', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OpeningHour::class,
            'error_bubbling' => true,
        ]);
    }
}
