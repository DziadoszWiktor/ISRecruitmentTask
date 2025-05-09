<?php

declare(strict_types=1);

namespace App\Config;

readonly class WorkTimeConfig
{
    /**
     * WorkTimeConfig construct
     *
     * @param int $normHours
     * @param float $hourlyRate
     * @param float $overtimeMultiplier
     */
    public function __construct(
        private int   $normHours,
        private float $hourlyRate,
        private float $overtimeMultiplier,
    ) {}

    /**
     * Get monthly norm of hours
     *
     * @return int
     */
    public function getNormHours(): int
    {
        return $this->normHours;
    }

    /**
     * Get hourly rate
     *
     * @return float
     */
    public function getHourlyRate(): float
    {
        return $this->hourlyRate;
    }

    /**
     * Get overtime multiplier
     *
     * @return float
     */
    public function getOvertimeMultiplier(): float
    {
        return $this->overtimeMultiplier;
    }
}
