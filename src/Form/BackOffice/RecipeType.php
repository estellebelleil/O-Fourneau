<?php

namespace App\Form\BackOffice;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\Ustensil;
use App\Form\BackOffice\QuantityType as BackOfficeQuantityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [

                'label' => 'Nom de la recette',
            ])
            ->add('description')
            ->add('picture', null, [

                'label' => 'URL de l\'image',
            ])
            ->add('step', null, [

                'label' => 'Etapes',
                'attr' => [
                    'class' => 'form-control',
                    
                ]
            ])
            ->add('ustensil', EntityType::class, [
                'class' => Ustensil::class,
                'choice_label' => 'name',
                'label' => 'Ustensiles nécessaires à la recette :',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
            ])
            ->add('tag', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie',
                'expanded' => true,
                'choice_label' => 'name',
            ])

            ->add('step', null, [

                'label' => 'Etapes',
            ])

            ->add ('quantities', CollectionType::class, [
                'label' => 'Liste des ingrédients',
                'attr' => ['class' => 'inject'],
                'entry_type' => BackOfficeQuantityType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true, // on peut ajouter des ingrédients
                'allow_delete' => true, // on peut supprimer des ingrédients
                'by_reference' => false, 
                'prototype' => true,
                
            ])




            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
                ]);

     
            }



            

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }


}
