<?php

namespace App\Form;

use App\Entity\OpeningHour;
use App\Form\OpeningHourType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpeningHoursFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('openingHours', CollectionType::class, [
                'entry_type' => OpeningHourType::class,
                'by_reference' => false,
                'error_bubbling' => false,
            ])
        ;
    }
}
