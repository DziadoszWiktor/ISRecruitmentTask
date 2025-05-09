<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\WorkTime;
use App\Exception\EmployeeNotFoundException;
use App\Exception\WorkTimeExceededException;
use App\Exception\DuplicatedWorkTimeException;
use App\Interface\EmployeeRepositoryInterface;
use App\Interface\WorkTimeRepositoryInterface;
use DateTime;
use DateTimeInterface;

class WorkTimeService
{
    private const DATE_FORMAT = 'Y-m-d';
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private readonly WorkTimeRepositoryInterface $workTimeRepository,
        private readonly EmployeeRepositoryInterface $employeeRepository
    ) {}

    /**
     * Register work time
     *
     * @param string $employeeId
     * @param DateTimeInterface $startedAtImmutable
     * @param DateTimeInterface $endedAtImmutable
     * @throws EmployeeNotFoundException
     * @throws WorkTimeExceededException
     * @throws DuplicatedWorkTimeException
     */
    public function register(
        string $employeeId,
        DateTimeInterface $startedAtImmutable,
        DateTimeInterface $endedAtImmutable
    ): void {
        $employee = $this->employeeRepository->find($employeeId);
        if (!$employee) {
            throw new EmployeeNotFoundException('Employee not found.');
        }

        $dayDate = $this->validateWorkTime(
            $employee,
            $startedAtImmutable,
            $endedAtImmutable
        );

        $startedAt = DateTime::createFromFormat(
            self::DATE_TIME_FORMAT,
            $startedAtImmutable->format(self::DATE_TIME_FORMAT)
        );
        $endedAt = DateTime::createFromFormat(
            self::DATE_TIME_FORMAT,
            $endedAtImmutable->format(self::DATE_TIME_FORMAT)
        );

        $workTime = new WorkTime();
        $workTime->setEmployee($employee);
        $workTime->setStartedAt($startedAt);
        $workTime->setEndedAt($endedAt);
        $workTime->setFirstDayDate($dayDate);

        $this->workTimeRepository->save($workTime);
    }

    /**
     * Validate work time parameters and business rules
     *
     * @param object $employee
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @return DateTime
     * @throws DuplicatedWorkTimeException
     * @throws WorkTimeExceededException
     */
    private function validateWorkTime(
        object $employee,
        DateTimeInterface $start,
        DateTimeInterface $end
    ): DateTime {
        if ($end <= $start) {
            throw new WorkTimeExceededException('End time must be after start time.');
        }
        $duration = $end->getTimestamp() - $start->getTimestamp();
        if ($duration > 12 * 3600) {
            throw new WorkTimeExceededException('Work time cannot exceed 12 hours.');
        }
        $dayDate = DateTime::createFromFormat(
            self::DATE_FORMAT,
            $start->format(self::DATE_FORMAT)
        );
        if (!$dayDate) {
            throw new WorkTimeExceededException('Invalid work day format.');
        }
        $existing = $this->workTimeRepository->findOneBy([
            'employee'     => $employee,
            'firstDayDate' => $dayDate,
        ]);
        if ($existing !== null) {
            throw new DuplicatedWorkTimeException('Work time entry for this day already exists.');
        }

        return $dayDate;
    }
}
