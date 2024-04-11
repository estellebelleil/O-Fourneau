<?php

namespace App\Form\BackOffice;

use App\Entity\Ingredient;
use App\Entity\Quantity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraints\Length;

class QuantityType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', null, [
                'label' => 'Quantité',
                'constraints' => [new Length(['min' => 1, 'max' => 3, 'exactMessage' => 'La quantité doit être comprise entre 1 et 3 caractères'])]




            ])
            ->add('ingredient', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => function (Ingredient $ingredient) {
                    return sprintf('%s (%s)', $ingredient->getName(), $ingredient->getUnit());
                },
                'multiple' => false,
                'expanded' => false,
                'label_attr' => ['class' => 'checkbox-inline'],


            ]);
    }




    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quantity::class,

        ]);
    }
}
