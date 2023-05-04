<?php

namespace App\Repository;

use App\Entity\HelpRequest;
use App\Entity\Statututilisateur;
use App\Entity\User;
use App\Entity\UserStatus;
use App\Service\HelpRequestTreatmentTypeLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry, public EntityManagerInterface $em)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findByApiToken(string $apiToken) : ?User
    {
        return $this->findOneBy([
            'tokenapi' => $apiToken,
            'status' => $this->em->getRepository(UserStatus::class)->findOneBy(
                [
                    'label' => 'Actif'
                ]
            )
        ]);
    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findHelperAcceptHelpRequest(HelpRequest $helpRequest)
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT u.id id FROM help_request hr
        INNER JOIN help_request_treatment hrt ON hr.id=hrt.help_request_id
        INNER JOIN user u ON u.id=hrt.helper_id
        INNER JOIN help_request_treatment_type hrtt ON hrt.type_id=hrtt.id
        WHERE help_request_id= :help_request_id
        AND hrtt.label = :label';
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'help_request_id' => $helpRequest->getId(),
            'label' => HelpRequestTreatmentTypeLabel::ACCEPTED->value
        ]);
        
        $helpRequestIds = $resultSet->fetchFirstColumn();

        $result = $this->findBy([
            'id' => $helpRequestIds
        ]);
        
        return $result;
    }
    public function findNbHelperAcceptHelpRequest(HelpRequest $helpRequest):int
    {

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(*) nb FROM help_request hr
        INNER JOIN help_request_treatment hrt ON hr.id=hrt.help_request_id
        INNER JOIN user u ON u.id=hrt.helper_id
        INNER JOIN help_request_treatment_type hrtt ON hrt.type_id=hrtt.id
        WHERE help_request_id= :help_request_id
        AND hrtt.label = :label';
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'help_request_id' => $helpRequest->getId(),
            'label' => HelpRequestTreatmentTypeLabel::ACCEPTED->value
        ]);
        
        $result = $resultSet->fetchFirstColumn();

        return (int)$result[0];
    }
    public function getLastMessageByUsers(User $userconnect)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT m.user_id, MAX(m.id) AS last_message_id FROM
                    (SELECT from_user_id as user_id, id
                    FROM message WHERE to_user_id=:user_id
                    UNION
                    SELECT to_user_id as user_id, id
                    FROM message WHERE from_user_id=:user_id) m
                GROUP BY m.user_id ORDER BY MAX(m.id) DESC;';
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'user_id' => $userconnect->getId()
        ]);
        
        $lastmessagebyuser = $resultSet->fetchAllAssociative();
        
        return $lastmessagebyuser;
    }
}
