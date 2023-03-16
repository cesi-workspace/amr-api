<?php

namespace App\Repository;

use App\Entity\Typetraitementdemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Typetraitementdemande>
 *
 * @method Typetraitementdemande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Typetraitementdemande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Typetraitementdemande[]    findAll()
 * @method Typetraitementdemande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypetraitementdemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Typetraitementdemande::class);
    }

    public function save(Typetraitementdemande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Typetraitementdemande $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Typetraitementdemande[] Returns an array of Typetraitementdemande objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Typetraitementdemande
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
