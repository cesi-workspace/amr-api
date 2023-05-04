<?php

namespace App\Repository;

use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Comment;

/**
 * @extends ServiceEntityRepository<Report>
 *
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function save(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Report $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Report[] Returns an array of Report objects
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

//    public function findOneBySomeField($value): ?Report
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function groupByComments() : array
    {
        $qb = $this->createQueryBuilder('r')
        ->select("count(*) as number_report, r.comment_id as comment_id")
        ->groupBy('r.comment_id');

        return $qb->getQuery()->getResult();
    }
    
    public function countReportByComment(Comment $comment) : int
    {

        $result = $this->createQueryBuilder('r')
            ->andWhere('r.comment = :comment')
            ->setParameter('comment', $comment)
            ->select('COUNT(r.user) as number_report')
            ->getQuery()
            ->getOneOrNullResult();

        return (int) $result['number_report'];
    }

    public function findReportByComment(Comment $comment)
    {
        $qb = $this->createQueryBuilder('r')
        ->andWhere('r.comment = :comment')
        ->setParameter('comment', $comment)
        ->getQuery();

        return $qb->getResult();
    }
}
