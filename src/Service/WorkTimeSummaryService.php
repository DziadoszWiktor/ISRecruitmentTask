<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidDateFormatException;
use App\Interface\WorkTimeRepositoryInterface;
use App\Config\WorkTimeConfig;
use DateMalformedStringException;
use DateTimeImmutable;
use InvalidArgumentException;

class WorkTimeSummaryService
{
    /**
     * WorkTimeSummaryService construct
     *
     * @param WorkTimeRepositoryInterface $workTimeRepository
     * @param WorkTimeConfig $config
     */
    public function __construct(
        private readonly WorkTimeRepositoryInterface $workTimeRepository,
        private readonly WorkTimeConfig              $config,
    ) {}

    /**
     * Summarize day
     *
     * @param string $employeeId
     * @param string $dateString
     * @return array
     */
    public function summarizeDay(string $employeeId, string $dateString): array
    {
        $day = DateTimeImmutable::createFromFormat('Y-m-d', $dateString);
        if (!$day) {
            throw new InvalidArgumentException('Invalid date format, expected Y-m-d');
        }

        $entries = $this->workTimeRepository->findByEmployeeAndDay($employeeId, $day);
        $totalMinutes = 0;

        foreach ($entries as $entry) {
            $interval = $entry->getEndedAt()->getTimestamp() - $entry->getStartedAt()->getTimestamp();
            $totalMinutes += intdiv($interval, 60);
        }

        $hours = $this->roundToHalfHours($totalMinutes);
        $rate = $this->config->getHourlyRate();
        $total = $hours * $rate;

        return [
            'hours' => $hours,
            'total' => $total,
            'rate' => $rate,
        ];
    }

    /**
     * Summarize month
     *
     * @param string $employeeId
     * @param string $monthString
     * @return array
     * @throws DateMalformedStringException
     * @throws InvalidDateFormatException
     */
    public function summarizeMonth(string $employeeId, string $monthString): array
    {
        $from = DateTimeImmutable::createFromFormat('Y-m', $monthString);
        if (!$from) {
            throw new InvalidDateFormatException('Invalid month format, expected Y-m');
        }

        $to = $from->modify('first day of next month')->modify('-1 second');

        $entries = $this->workTimeRepository->findByDateRange($employeeId, $from, $to);
        $totalMinutes = 0;

        foreach ($entries as $entry) {
            $interval = $entry->getEndedAt()->getTimestamp() - $entry->getStartedAt()->getTimestamp();
            $totalMinutes += intdiv($interval, 60);
        }

        $totalHours = $this->roundToHalfHours($totalMinutes);
        $norm = $this->config->getNormHours();
        $hourly = $this->config->getHourlyRate();
        $overtimeRate = $hourly * $this->config->getOvertimeMultiplier();

        $normalHours = min($totalHours, $norm);
        $overtimeHours = max(0.0, $totalHours - $norm);

        return [
            'normal_hours' => $normalHours,
            'overtime_hours'=> $overtimeHours,
            'normal_rate' => $hourly,
            'overtime_rate' => $overtimeRate,
            'total' => $normalHours * $hourly + $overtimeHours * $overtimeRate,
        ];
    }

    /**
     * Hour round system
     *
     * @param int $minutes
     * @return float
     */
    private function roundToHalfHours(int $minutes): float
    {
        return round($minutes / 30) * 0.5;
    }
}
