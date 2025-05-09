<?php

declare(strict_types=1);

namespace App\Interface;

use App\Entity\WorkTime;
use DateTimeInterface;

interface WorkTimeRepositoryInterface
{
    /**
     * Save work time
     *
     * @param WorkTime $workTime
     * @return void
     */
    public function save(WorkTime $workTime): void;

    /**
     * Find employee
     *
     * @param string $employeeId
     * @param DateTimeInterface $day
     * @return array
     */
    public function findByEmployeeAndDay(string $employeeId, DateTimeInterface $day): array;

    /**
     * Find date range
     *
     * @param string $employeeId
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @return array
     */
    public function findByDateRange(string $employeeId, DateTimeInterface $from, DateTimeInterface $to): array;
}
