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
     * @param string $employeeId
     * @param DateTimeInterface $day
     * @return array
     */
    public function findByEmployeeAndDay(string $employeeId, DateTimeInterface $day): array
    {
        return [];
    }

    /**
     * @param string $employeeId
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @return array
     */
    public function findByDateRange(string $employeeId, DateTimeInterface $from, DateTimeInterface $to): array
    {
        return [];
    }
}
