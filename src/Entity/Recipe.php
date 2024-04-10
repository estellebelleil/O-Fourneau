<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get_recipes', 'get_comments', 'get_users'])]
    private ?int $id = null;

    //Name
    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'Le titre de la recette doit faire au minimum 5 caracteres',
        maxMessage: 'Le titre de la recette doit faire au maximum 50 caracteres',
    )]
    #[Groups(['get_recipes', 'get_comments', 'get_users'])]
    private ?string $name = null;


    //Description
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 15,
        max: 1000,
        minMessage: 'La description de la recette doit faire au minimum 15 caracteres',
        maxMessage: 'La description de la recette doit faire au maximum 1000 caracteres',
    )]
    #[Groups(['get_recipes', 'get_users'])]
    private ?string $description = null;


    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['get_recipes', 'get_users'])]
    private ?string $picture = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['get_recipes', 'get_users'])]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Ustensil::class, inversedBy: 'recipes')]
    #[Groups(['get_recipes', 'get_users'])]
    private Collection $ustensil;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'recipes')]
    #[Groups(['get_recipes', 'get_users'])]
    private Collection $tag;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[Assert\NotBlank()]
    #[Groups(['get_recipes', 'get_users'])]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Comment::class)]

    #[Groups(['get_recipes', 'get_users'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Quantity::class,cascade: ['persist'])]
    #[Assert\Count(
        min: 1,
        max: 25,
        minMessage: 'Vous devez insérer au minimum un ingrédient',
        maxMessage: 'Vous ne pouvez pas dépasser 25 ingrédients'
    )]
    #[Groups(['get_recipes', 'get_ingredients', 'get_users'])]
    private Collection $quantities;

    #[ORM\Column(nullable: true)]
    #[Groups(['get_recipes', 'get_users'])]
    private ?int $rate = null;

    #[ORM\Column]
    #[Groups(['get_recipes', 'get_users'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['get_recipes', 'get_users'])]
    private ?\DateTimeImmutable $update_at = null;

    #[ORM\ManyToOne(inversedBy: 'recipe')]
    #[Groups(['get_recipes'])]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        min: 20,
        minMessage: 'Les étapes de la recette doivent faire au minimum 20 caracteres',
    )]
    #[Groups(['get_recipes', 'get_users'])]
    private ?string $step = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'recipesFavorites')]

    private Collection $recipesFavorites;


    public function __construct()
    {
        $this->ustensil = new ArrayCollection();
        $this->tag = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->quantities = new ArrayCollection();
        $this->recipesFavorites = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Ustensil>
     */
    public function getUstensil(): Collection
    {
        return $this->ustensil;
    }

    public function addUstensil(Ustensil $ustensil): static
    {
        if (!$this->ustensil->contains($ustensil)) {
            $this->ustensil->add($ustensil);
        }

        return $this;
    }

    public function removeUstensil(Ustensil $ustensil): static
    {
        $this->ustensil->removeElement($ustensil);
        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tag->contains($tag)) {
            $this->tag->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tag->removeElement($tag);

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setRecipe($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRecipe() === $this) {
                $comment->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Quantity>
     */
    public function getQuantities(): Collection
    {
        return $this->quantities;
    }

    public function addQuantity(Quantity $quantity): static
    {
        if (!$this->quantities->contains($quantity)) {
            $this->quantities[] = $quantity;
            $quantity->setRecipe($this);
        }

        return $this;
    }

    public function removeQuantity(Quantity $quantity): static
    {
        if ($this->quantities->removeElement($quantity)) {
            // set the owning side to null (unless already changed)
            if ($quantity->getRecipe() === $this) {
                $quantity->setRecipe(null);
            }
        }

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->update_at;
    }

    public function setUpdateAt(?\DateTimeImmutable $update_at): static
    {
        $this->update_at = $update_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */

    public function getStep(): ?string
    {
        return $this->step;
    }

    public function setStep(?string $step): static
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRecipesFavorites(): Collection
    {
        return $this->recipesFavorites;
    }

    public function addRecipesFavorite(User $recipesFavorite): static
    {
        if (!$this->recipesFavorites->contains($recipesFavorite)) {
            $this->recipesFavorites->add($recipesFavorite);
        }

        return $this;
    }

    public function removeRecipesFavorite(User $recipesFavorite): static
    {
        $this->recipesFavorites->removeElement($recipesFavorite);

        return $this;
    }
}
