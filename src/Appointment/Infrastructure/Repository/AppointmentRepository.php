<?php

namespace App\Appointment\Infrastructure\Repository;

use App\Appointment\Domain\Repository\AppointmentRepositoryInterface;
use App\Appointment\Infrastructure\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Appointment\Domain\Model\AppointmentStatus;
use App\User\Infrastructure\Entity\User;

/**
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository implements AppointmentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function checkAvailability(Appointment $appointment)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
            ->from($this->_entityName, 'a')
            ->where('a.lawyerId = :lawyerId')
            ->andWhere('a.status = :status')
            ->andWhere('a.request_time BETWEEN :startTime AND :endTime')
            ->setMaxResults(1)
            ->setParameter('lawyerId', $appointment->getLawyerId())
            ->setParameter('status', $appointment->getStatus())
            ->setParameter('startTime', $appointment->getRequestTime()->format('Y-m-d H:i:s'))
            ->setParameter('endTime', $appointment->getRequestTime()->modify('+1 hour')->format('Y-m-d H:i:s'));


        return $qb->getQuery()->getResult() ? false : true;
    }

    public function save(Appointment $appointment)
    {
        $this->getEntityManager()->persist($appointment);
        $this->getEntityManager()->flush();
    }

    public function fetchAll($currentPage = 1, $limit = 10)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
            ->from($this->_entityName, 'a');

        $paginator = new Paginator($qb);

        $paginator->getQuery()
            ->setFirstResult($limit * ($currentPage - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
    
    public function fetchRecent(int $citizenId)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
            ->from($this->_entityName, 'a')
            ->where('a.citizenId = :citizenId')
            ->andWhere('a.status = :approved')
            ->orWhere('a.status = :rejected')
            ->add('orderBy','a.id DESC')
            ->setMaxResults(5)
            ->setParameter('citizenId', $citizenId)
            ->setParameter('approved', AppointmentStatus::APPROVED)
            ->setParameter('rejected', AppointmentStatus::REJECTED);


        return $qb->getQuery()->getResult();

    }
}
