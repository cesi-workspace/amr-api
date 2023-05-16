<?php

namespace App\Repository;

use App\Entity\HelpRequest;
use App\Entity\User;
use App\Service\HelpRequestStatusLabel;
use App\Service\HelpRequestTreatmentTypeLabel;
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
        $maxnbresult = $parameters['max_nb_results'];
        $helper = $parameters['helper'];
        unset($parameters['helper']);
        unset($parameters['max_nb_results']);
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT hr.id FROM help_request hr
        INNER JOIN help_request_category hrc ON hr.category_id=hrc.id
        INNER JOIN help_request_status hrs ON hr.status_id=hrs.id';

        if($helper){
            $sql = $sql.' LEFT JOIN help_request_treatment hrt ON hrt.help_request_id=hr.id AND hrt.helper_id='.$helper->getId();
        }

        $sql = $sql.' WHERE 1=1';
        
        if(array_key_exists('latitude', $parameters) && array_key_exists('longitude', $parameters) && array_key_exists('range', $parameters)){
            $sql = $sql.' AND get_distance_kms(latitude, longitude, :latitude, :longitude) <= :range';
        }
        if(array_key_exists('category', $parameters)){
            $sql = $sql.' AND hrc.title = :category';
        }
        if(array_key_exists('status', $parameters)){
            $sql = $sql.' AND hrs.label = :status';
        }
        if(array_key_exists('owner_id', $parameters)){
            $sql = $sql.' AND hr.owner_id = :owner_id';
        }
        if(array_key_exists('helper_id', $parameters)){
            $sql = $sql.' AND hr.helper_id = :helper_id';
        }
        if($helper){
            $sql = $sql.' AND (hrt.type_id IS NULL OR hrt.type_id !=3)';
        }
        if(array_key_exists('latitude', $parameters) && array_key_exists('longitude', $parameters)){
            $sql = $sql. ' ORDER BY get_distance_kms(latitude, longitude, :latitude, :longitude)';
        }else{
            $sql = $sql. ' ORDER BY hr.created_at DESC';
        }
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($parameters);
        
        $helpRequestIds = $resultSet->fetchFirstColumn();
        
        $result = $this->findBy([
            'id' => $helpRequestIds
        ], [], (int)$maxnbresult);
        
        // Obligation de retrier manuellement sur la liste d'objets obtenues car par défaut findBy va trier par l'id en croissant
        // On trie selon l'ordre des ids récupérés dans la liste
        usort($result, function($val1, $val2) use($helpRequestIds) {
            if (array_search($val1->getId(), $helpRequestIds) > array_search($val2->getId(), $helpRequestIds)){
                return 1;
            }else{
                if (array_search($val1->getId(), $helpRequestIds) < array_search($val2->getId(), $helpRequestIds)){
                    return -1;
                }else{
                    return 0;
                }
            }
            
        });
        return $result;
    }

    public function findHelpRequestByTreatmentTypeUser(string $helpRequestTreatmentType, User $user)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT hr.id FROM help_request hr
        INNER JOIN help_request_status hrs ON hrs.id=hr.status_id
        INNER JOIN help_request_treatment hrt ON hrt.help_request_id=hr.id
        INNER JOIN help_request_treatment_type hrtt ON hrt.type_id=hrtt.id
        WHERE hrt.helper_id=:helper_id AND hrtt.label=:label AND hrs.label=:label2
        ORDER BY hrt.created_at DESC';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['helper_id' => $user->getId(), 'label' => $helpRequestTreatmentType, 'label2' => HelpRequestStatusLabel::CREATED->value]);

        
        $helpRequestIds = $resultSet->fetchFirstColumn();

        $result = $this->findBy([
            'id' => $helpRequestIds
        ]);

        return $result;
    }

    
    public function findHelpRequestsStatsByCriteria($parameters)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT COUNT(*) AS nb_help_request_total,
        SUM(IF(status_id=3, 1, 0)) AS nb_help_request_finish,
        DATE_FORMAT(hr.created_at,'%Y-%m') AS month FROM help_request hr
        INNER JOIN help_request_category hrc ON hrc.id=hr.category_id
        WHERE 1=1";
        
        if(array_key_exists('category', $parameters)){
            $sql = $sql.' AND hrc.title = :category';
        }
        if(array_key_exists('owner_id', $parameters)){
            $sql = $sql.' AND hr.owner_id = :owner_id';
        }
        if(array_key_exists('helper_id', $parameters)){
            $sql = $sql.' AND hr.helper_id = :helper_id';
        }
        if(array_key_exists('start_date', $parameters)){
            $sql = $sql.' AND hr.created_at >= :start_date';
        }
        if(array_key_exists('end_date', $parameters)){
            $sql = $sql.' AND hr.created_at <= :end_date';
        }
        if(array_key_exists('latitude', $parameters) && array_key_exists('longitude', $parameters) && array_key_exists('range', $parameters)){
            $sql = $sql.' AND get_distance_kms(latitude, longitude, :latitude, :longitude) <= :range';
        }
        $sql = $sql. " GROUP BY DATE_FORMAT(hr.created_at,'%Y-%m') ORDER BY DATE_FORMAT(hr.created_at,'%Y-%m')";
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($parameters);
        $result=$resultSet->fetchAllAssociative();

        return $result;
    }
}
