<?php
// src/Service/MySlugger.php
namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Service qui concerne les slugs
 */
class MySlugger
{
    private $slugger;
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * Fonction qui slugifie une string
     *
     * @return string
     */
    public function slugify(string $title): string
    {
        // On slugifie le parametre $title de la fonction
        $slug = $this->slugger->slug($title)->lower();
        // On retourne ça
        return $slug;
    }
}