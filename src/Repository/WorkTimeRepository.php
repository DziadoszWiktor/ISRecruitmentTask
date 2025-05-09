<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\WorkTime;
use App\Interface\WorkTimeRepositoryInterface;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        $qb = $this->createQueryBuilder('w')
            ->join('w.employee', 'e')
            ->andWhere('e.id = :employee_id')
            ->andWhere('w.firstDayDate = :day')
            ->setParameter('employee_id', $employeeId)
            ->setParameter('day', $day->format('Y-m-d'));

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritDoc
     */
    public function findByDateRange(string $employeeId, DateTimeInterface $from, DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('w')
            ->join('w.employee', 'e')
            ->andWhere('e.id = :employee_id')
            ->andWhere('w.startedAt >= :from')
            ->andWhere('w.endedAt   <= :to')
            ->setParameter('employee_id', $employeeId)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        return $qb->getQuery()->getResult();
    }
}
