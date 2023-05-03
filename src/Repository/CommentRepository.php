<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function save(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Comment[] Returns an array of Comment objects
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

//    public function findOneBySomeField($value): ?Comment
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findCommentsByCriteria($parameters)
    {
        $owner = array_key_exists('owner_id', $parameters) ? $parameters['owner_id'] : null;
        $helper = array_key_exists('helper_id', $parameters) ? $parameters['helper_id'] : null;
        $startDate = array_key_exists('start_date', $parameters) ? new \DateTime($parameters['start_date']) : null;
        $endDate = array_key_exists('end_date', $parameters) ? new \DateTime($parameters['end_date']) : null;

        $query = $this->createQueryBuilder('c');

        if($owner != null){
            $query
            ->andWhere('identity(c.owner) = :val1')
            ->setParameter('val1', $owner);
        }
            
        if($helper != null){
            $query
            ->andWhere('identity(c.helper) = :val2')
            ->setParameter('val2', $helper);
        }
        if($startDate != null){
            $query
            ->andWhere('c.date >= :val3')
            ->setParameter('val3', $startDate);
        }
        if($endDate != null){
            $query
            ->andWhere('c.date <= :val4')
            ->setParameter('val4', $endDate);
        }
        
        $result = $query
        ->orderBy('c.date', 'DESC')
        ->getQuery()
        ->getResult();

        return $result;
    }
}
