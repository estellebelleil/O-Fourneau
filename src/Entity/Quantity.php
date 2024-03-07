<?php

namespace App\Entity;

use App\Repository\QuantityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuantityRepository::class)]
class Quantity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_ingredients', 'get_recipes', 'get_users'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'quantities')]
    #[Assert\Count(
        min: 1,
        minMessage: 'Vous devez choisir au moins un ingrédient',
    )]
    #[Groups(['get_ingredients','get_recipes', 'get_users'])]
    private ?Ingredient $ingredient = null;


    #[ORM\ManyToOne(inversedBy: 'quantities')]
    #[Groups(['get_ingredients'])]
    private ?Recipe $recipe = null;

    
    #[ORM\Column]
    #[Assert\Length(
        min: 1,
        max: 10000,
        minMessage: 'La quantité doit être supérieure à 0',
        maxMessage: 'La quantité doit être supérieure à 1000'
    )]
    #[Groups(['get_quantities','get_recipes', 'get_users'])]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

   
}