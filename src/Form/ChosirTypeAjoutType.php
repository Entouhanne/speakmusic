<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class ChosirTypeAjoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('type', ChoiceType::class, [
            'choices'  => [
                'Genre' => 'genre',
                'Auteur' => 'auteur',
                'Album' => 'album',
                'Musique' => 'musique',
            ],
            
            'choice_attr' => function ($choice, $key, $value) {
                // adds a class like attending_yes, attending_no, etc
                return ['class' => 'attending_'.strtolower($key)];
            },
            ])
            ->add('submit', SubmitType::class, [
                ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
