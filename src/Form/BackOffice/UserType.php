<?php

namespace App\Form\BackOffice;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')

            ->add('name')

            
            // roles est avant tout un tableau qui va contenir tous les droits qu'un user peut avoir, on en fait donc une checkbox
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'ADMIN' => 'ROLE_ADMIN',
                    'MANAGER' => 'ROLE_MANAGER',
                    'USER' => 'ROLE_USER',
                ],
                'label' => "Selectionnez que 1, pas besoin d'en mettre plusieurs ",
                // Lorsqu'on veut qu'un champ ait plusieurs valeures potentielles 
                // par ex : ROLE_ADMIN ET ROLE_USER (mais aucun interet pour nous, car on a mit en place la hierarchisation)
                'expanded' => true, 
                'multiple' => true,

            
            ])
            // ->add('password')
            
            

            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
        
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
