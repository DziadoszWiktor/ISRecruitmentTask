<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\WorkTime;
use App\Interface\WorkTimeRepositoryInterface;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Doctrine\DBAL\Types\Types;

/**
 * @extends ServiceEntityRepository<WorkTime>
 */
class WorkTimeRepository extends ServiceEntityRepository implements WorkTimeRepositoryInterface
{
    /**
     * WorkTimeRepository construct
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkTime::class);
    }

    /**
     * WorkTime save
     *
     * @param WorkTime $workTime
     * @return void
     */
    public function save(WorkTime $workTime): void
    {
        $em = $this->getEntityManager();
        $em->persist($workTime);
        $em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findByEmployeeAndDay(string $employeeId, DateTimeInterface $day): array
    {
        return $this->createQueryBuilder('w')
            ->join('w.employee', 'e')
            ->andWhere('e.id = :employee_id')
            ->andWhere('w.firstDayDate = :day')
            ->setParameter('employee_id', Uuid::fromString($employeeId), UuidType::NAME)
            ->setParameter('day', $day, Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findByDateRange(string $employeeId, DateTimeInterface $from, DateTimeInterface $to): array
    {
        return $this->createQueryBuilder('w')
            ->join('w.employee', 'e')
            ->andWhere('e.id = :employee_id')
            ->andWhere('w.startedAt >= :from')
            ->andWhere('w.endedAt   <= :to')
            ->setParameter('employee_id', Uuid::fromString($employeeId), UuidType::NAME)
            ->setParameter('from', $from, Types::DATETIME_IMMUTABLE)
            ->setParameter('to',   $to,   Types::DATETIME_IMMUTABLE)
            ->getQuery()
            ->getResult();
    }
}
