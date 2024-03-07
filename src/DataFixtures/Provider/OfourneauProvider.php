<?php

namespace App\DataFixtures\Provider;

class OfourneauProvider
{
    // Tableau des 3 catégories
    private $categories = [
        'Entrée',
        'Plat',
        'Dessert'
    ];
    private $quantities = [
        25, 50, 75, 100, 125, 150, 175, 200, 225, 250, 
        275, 300, 325, 350, 375, 400, 425, 450, 475, 500, 
        525, 550, 575, 600, 800, 900, 1000
    ];
    // Tableau d'unité de mesure
    private $units = [
        'gr',
        'kg',
        'L',
        'mL',
        'c. à café',
        'c. à soupe',
        'tasse',
        'verre',
        'cl',
        'pincée',
        'filet',
        'tranche',
        'brin',
        'feuille',
        'gousse',
        'bouquet',
        'bouteille',
        'pinte',
        'quart'
    ];
    // Tableau des tags possibles
    private $tags = [
        'Végétarien',
        'Réconfortant',
        'Eté',
        'Hiver',
        'Automne',
        'Printemps',
        'Rapide',
        'Facile',
        'Moyen',
        'Difficile',
        'Sans-gluten',
        'Végétalien',
        'Peu calorique',
        'Cuisine du monde'
    ];
    // Tableau d'ustensils
    private $ustensils = [
        'Moule',
        'Casseroles',
        'Spatule',
        'Cuillère en bois',
        'Fouet',
        'Louche',
        'Couteau de chef',
        'Planche à découper',
        'Passoire',
        'Écumoire',
        'Pelle à pizza',
        'Cuillère à mesure',
        'Balance de cuisine',
        'Entonnoir',
        'Presse-ail',
        'Épluche-légumes',
        'Économe',
        'Mandoline',
        'Râpe',
        'Fouet électrique',
        'Batteur électrique',
        'Mixeur plongeant'
    ];
    // Tableau des ingredients
        private $ingredients = [
            "Carotte",
            "Tomate",
            "Pomme de terre",
            "Courgette",
            "Poivron",
            "Aubergine",
            "Brocoli",
            "Haricot vert",
            "Poireau",
            "Poulet",
            "Bœuf",
            "Porc",
            "Agneau",
            "Canard",
            "Veau",
            "Saumon",
            "Thon",
            "Dorade",
            "Bar",
            "Cabillaud",
            "Crozet",
            "Quinoa",
            "Blé",
            "Patates douces",
            "Sel",
            "Poivre",
            "Huile d'olive",
            "Oignon",
            "Ail",
            "Beurre",
            "Sucre",
            "Sucre-glace",
            "Farine",
            "Vinaigre",
            "Citron",
            "Miel",
            "Crème fraîche",
            "Pâtes",
            "Riz",
            "Fromage râpé",
            "Basilic",
            "Persil",
            "Noisettes",
            "Amandes",
            "Noix de cajou",
            "Pignons de pin",
            "Banane",
            "Pomme",
            "Orange",
            "Fraise",
            "Ananas",
            "Kiwi",
            "Mangue",
            "Raisin"
        ];

        private $recipesName = [
            "Boeuf bourguignon",
            "Poulet rôti",
            "Spaghetti carbonara",
            "Salade César",
            "Ratatouille",
            "Quiche lorraine",
            "Lasagnes",
            "Tarte aux pommes",
            "Sushi",
            "Burger",
            "Moussaka",
            "Poulet tikka masala",
            "Couscous",
            "Tacos",
            "Gâteau au chocolat",
            "Salade niçoise",
            "Paella",
            "Poulet tandoori",
            "Biryani",
            "Tarte au citron",
            "Gnocchi",
            "Crevettes grillées",
            "Fondue savoyarde",
            "Risotto aux champignons",
            "Pizza margherita",
            "Tiramisu",
            "Poulet au curry",
            "Soupe à l'oignon",
            "Chili con carne",
            "Sushi california"
        ];

        private $tips = [
            "Des herbes fraîches pour rehausser le goût.",
            "Maîtrisez la cuisson des œufs.",
            "Préparez ses repas à l'avance.",
            "Des restes pour éviter le gaspillage.",
            "Oui, aux épices exotiques !",
            "Des sauces maisons ? Rien de plus simple !",
            "Gardez vos couteaux bien aiguisés.",
            "Bien cuire ses légumineuses."
        ];
    /**
     * Retourne un ustensil
     */
    public function ustensils(): array
    {
        return $this->ustensils;
    }

    /**
     * Retourne un ingrédient
     */
    public function ingredients(): array
    {
        return $this->ingredients;
    }
     /**
     * Retourne une quantité
     */
    public function quantities(): array
    {
        return $this->quantities;
    }
    /**
     * Retourne une unité
     */
    public function units(): array
    {
        return $this->units;
    }
    /**
     * Retourne un tips
     */
    public function tips(): array
    {
        return $this->tips;
    }
    /**
     * Retourne un tag
     */
    public function tags(): array
    {
        return $this->tags;
    }
    /**
     * Retourne une catégorie
     *
     */
    public function categories(): array
    {
        return $this->categories;
    }
    /**
     * Retourne un ustensil
     */
    public function recipesNames(): array
    {
        return $this->recipesName;
    }
    /**
     * Retourne une categorie au hasard
     *
     * @return void
     */
    public function category_rand()
    {
        // Genere un nombre au hasard entre 0 et et 299 (car 300 films)
        $rand = rand(0, 2);
        // Retourne le film qui a pour index la valeur random
        return $this->categories[$rand];
    }

    /**
     * Retourne un genre au hasard
     *
     * @return void
     */
    public function ustensil_rand()
    {
        // Genere un nombre au hasard entre 0 et et 79 (car 80 genres)
        $rand = rand(0, 22);
        // Retourne le film qui a pour index la valeur random
        return $this->ustensils[$rand];
    }
     /**
     * Retourne un genre au hasard
     *@return void
     */
    public function unit_rand()
    {
        // Genere un nombre au hasard entre 0 et et 79 (car 80 genres)
        $rand = rand(0, 18);
        // Retourne le film qui a pour index la valeur random
        return $this->units[$rand];
    }
         /**
     * Retourne un genre au hasard
     *
     */
    public function quantity_rand()
    {
        // Genere un nombre au hasard entre 0 et et 79 (car 80 genres)
        $rand = rand(0, 39);
        // Retourne le film qui a pour index la valeur random
        return $this->quantities[$rand];
    }

}
