<?php

namespace App\Repository;

use App\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Character|null find($id, $lockMode = null, $lockVersion = null)
 * @method Character|null findOneBy(array $criteria, array $orderBy = null)
 * @method Character[]    findAll()
 * @method Character[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    public function findAllNotDeleted(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findByName(string $name)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name LIKE :name')
            ->andWhere('c.deletedAt IS NULL')
            ->setParameter('name', '%'.$name.'%')
            ->getQuery()
            ->getResult();
    }
}
