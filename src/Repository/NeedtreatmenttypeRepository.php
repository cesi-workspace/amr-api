<?php

namespace App\Repository;

use App\Entity\Needtreatmenttype;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Needtreatmenttype>
 *
 * @method Needtreatmenttype|null find($id, $lockMode = null, $lockVersion = null)
 * @method Needtreatmenttype|null findOneBy(array $criteria, array $orderBy = null)
 * @method Needtreatmenttype[]    findAll()
 * @method Needtreatmenttype[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NeedtreatmenttypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Needtreatmenttype::class);
    }

    public function save(Needtreatmenttype $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Needtreatmenttype $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Needtreatmenttype[] Returns an array of Needtreatmenttype objects
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

//    public function findOneBySomeField($value): ?Needtreatmenttype
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
