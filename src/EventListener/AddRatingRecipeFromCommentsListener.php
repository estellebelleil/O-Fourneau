<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Entity\Comment;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Comment::class)]
final class AddRatingRecipeFromCommentsListener
{
    /**
     * Méthode qui s'execute au postPersist d'une Comment
     */
    public function postPersist(Comment $comment, PostPersistEventArgs $event): void
    {
        // 1ere etape : on recupere le recipe via $comment
        $recipe = $comment->getRecipe();
        // 2eme etape : on calcule la note global de toutes les Comments du film
        // Je créer une variable $allNotes egal a 0
        // Cette variable sera egal à la somme de toutes les notes d'un film
        $allNotes = 0;
        // Je boucle sur toutes les Comments d'un film
        foreach ($recipe->getComments() as $comment) {
            $allNotes = $allNotes + $comment->getRate();
        }
        if($allNotes == 0 )
        {
            $recipe->setRate(0);

        }
        else
        {
                    // Pour une moyenne on divise le nombre total par le nombre de note
            $average = $allNotes / count($recipe->getComments());        
            $recipe->setRate($average);
        }

        // Pour récupérer l'entityManager : https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-listeners
        $entityManager = $event->getObjectManager();
        // Et grace a l'entityManager, je peux flush
        $entityManager->flush();
    }
}
