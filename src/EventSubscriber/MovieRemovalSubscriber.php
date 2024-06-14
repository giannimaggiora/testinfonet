<?php

namespace App\EventSubscriber;

use App\Entity\Movie;
use App\Entity\MovieCharacter;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\EntityManagerInterface;

class MovieRemovalSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $this->entityManager;
        $uow = $em->getUnitOfWork();
        $movieRepository = $em->getRepository(Movie::class);

        // Verificar las entidades pendientes de eliminación
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof MovieCharacter) {
                $movie = $entity->getMovie();
                $remainingCharacters = $movieRepository->createQueryBuilder('m')
                    ->select('mc')
                    ->from(MovieCharacter::class, 'mc')
                    ->where('mc.movie = :movie')
                    ->setParameter('movie', $movie)
                    ->getQuery()
                    ->getResult();

                // Si no quedan personajes asociados a la película, eliminar la película
                if (count($remainingCharacters) === 0) {
                    $em->remove($movie);
                    $uow->scheduleForDelete($movie);
                }
            }
        }
    }
}
