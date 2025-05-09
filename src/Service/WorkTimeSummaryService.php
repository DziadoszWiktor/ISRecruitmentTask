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
     * WorkTimeSummaryService
     *
     * @param WorkTimeRepositoryInterface $workTimeRepository
     * @param WorkTimeConfig $config
     */
    public function __construct(
        private readonly WorkTimeRepositoryInterface $workTimeRepository,
        private readonly WorkTimeConfig $config
    ) {}

    /**
     * Summarize work time for a given day.
     *
     * @param string $employeeId
     * @param string $dateString
     * @return array{hours: float, total: float, rate: float}
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
     * Summarize work time for a given month.
     *
     * @param string $employeeId
     * @param string $monthString Format 'Y-m'
     * @return array{normal_hours: float, overtime_hours: float, normal_rate: float, overtime_rate: float, total: float}
     * @throws InvalidDateFormatException|DateMalformedStringException
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
        dd($norm);
        $hourly = $this->config->getHourlyRate();
        $overtimeRate = $hourly * $this->config->getOvertimeMultiplier();

        $normalHours = min($totalHours, $norm);
        $overtimeHours = max(0.0, $totalHours - $norm);

        $normalTotal = $normalHours * $hourly;
        $overtimeTotal = $overtimeHours * $overtimeRate;

        return [
            'normal_hours' => $normalHours,
            'overtime_hours' => $overtimeHours,
            'normal_rate' => $hourly,
            'overtime_rate' => $overtimeRate,
            'total' => $normalTotal + $overtimeTotal,
        ];
    }

    /**
     * Round minutes to nearest half-hour
     *
     * @param int $minutes
     * @return float
     */
    private function roundToHalfHours(int $minutes): float
    {
        return round($minutes / 30) * 0.5;
    }
}