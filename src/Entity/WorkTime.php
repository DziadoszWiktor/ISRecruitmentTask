<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkTimeRepository::class)]
class WorkTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'workTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $endedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTime $firstDayDate = null;

    /**
     * Get work time Id
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get employee
     *
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    /**
     * Set employee
     *
     * @param Employee|null $employee
     * @return void
     */
    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * Get started at DateTime work
     *
     * @return DateTime|null
     */
    public function getStartedAt(): ?DateTime
    {
        return $this->startedAt;
    }

    /**
     * Set started at DateTime work
     *
     * @param DateTime $startedAt
     * @return void
     */
    public function setStartedAt(DateTime $startedAt): void
    {
        $this->startedAt = $startedAt;
    }

    /**
     * Get ended at DateTime work
     *
     * @return DateTime|null
     */
    public function getEndedAt(): ?DateTime
    {
        return $this->endedAt;
    }

    /**
     * Set ended at DateTime work
     *
     * @param DateTime $endedAt
     * @return void
     */
    public function setEndedAt(DateTime $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    /**
     * Get first work day date
     *
     * @return DateTime|null
     */
    public function getFirstDayDate(): ?DateTime
    {
        return $this->firstDayDate;
    }

    /**
     * Set first work day date
     *
     * @param DateTime $firstDayDate
     * @return void
     */
    public function setFirstDayDate(DateTime $firstDayDate): void
    {
        $this->firstDayDate = $firstDayDate;
    }

    /**
     * Set callback for firstDayDate filed
     *
     * @return void
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeFirstDayDate(): void
    {
        if (null === $this->firstDayDate && null !== $this->startedAt) {
            $this->firstDayDate = DateTime::createFromFormat('Y-m-d', $this->startedAt->format('Y-m-d'));
        }
    }
}
