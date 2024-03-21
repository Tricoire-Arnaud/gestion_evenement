<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                    ->add('title', null, [
                        'label' => 'Titre',
                    ])
                    ->add('description', null, [
                        'label' => 'Description',
                    ])
                    ->add('beginDate', null, [
                        'label' => 'Date de début',
                        'widget' => 'single_text',
                    ])
                    ->add('endDate', null, [
                        'label' => 'Date de fin',
                        'widget' => 'single_text',
                    ])
                    ->add('place', null, [
                        'label' => 'Lieu',
                    ])
                    ->add('creator', EntityType::class, [
                        'label' => 'Créateur',
                        'class' => User::class,
                        'choice_label' => 'name', 
                    ])
                ;
            }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
