<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Entity\Comment;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Comment::class)]

final class RemoveRatingRecipeFromCommentsListener
{
    /**
     * Méthode qui s'execute au postPersist d'une Comment
     */
    public function postRemove(Comment $comment, PostRemoveEventArgs $event): void
    {

        $recipe = $comment->getRecipe();
        $allNotes = 0;
        foreach ($recipe->getComments() as $comment) {
            $allNotes = $allNotes + $comment->getRate();
        }
        if($allNotes == 0 )
        {
            $recipe->setRate(0);
        }
        else
        {
            $average = $allNotes / count($recipe->getComments());        
            $recipe->setRate($average);
        }

        // Pour récupérer l'entityManager : https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-listeners
        $entityManager = $event->getObjectManager();
        // Et grace a l'entityManager, je peux flush
        $entityManager->flush();
    }
}
