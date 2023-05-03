<?php

namespace App\Repository;

use App\Entity\HelpRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HelpRequest>
 *
 * @method HelpRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method HelpRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method HelpRequest[]    findAll()
 * @method HelpRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HelpRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HelpRequest::class);
    }

    public function save(HelpRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HelpRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Need[] Returns an array of Need objects
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

//    public function findOneBySomeField($value): ?Need
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findHelpRequestsByCriteria($parameters)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT hr.id FROM help_request hr INNER JOIN help_request_category hrc ON hr.category_id=hrc.id WHERE get_distance_kms(latitude, longitude, :latitude, :longitude) <= :range';
        
        if(array_key_exists('category', $parameters)){
            $sql = $sql.' AND hrc.title = :category';
        }
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($parameters);
        // returns an array of arrays (i.e. a raw data set)
        $helpRequestIds = $resultSet->fetchFirstColumn();

        $result = $this->findBy([
            'id' => $helpRequestIds
        ]);
        
        return $result;
    }
}
