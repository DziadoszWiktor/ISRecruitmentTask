<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $surname = null;

    /** @var Collection<int, WorkTime> */
    #[ORM\OneToMany(targetEntity: WorkTime::class, mappedBy: 'employee', cascade: ['persist', 'remove'])]
    private Collection $workTimes;

    /**
     * Employee construct
     */
    public function __construct()
    {
        $this->workTimes = new ArrayCollection();
    }

    /**
     * Get employee Id
     *
     * @return Uuid|null
     */
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * Get employee name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set employee name
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get employee surname
     *
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Set employee name
     *
     * @param string $surname
     * @return void
     */
    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    /**
     * Get work times
     *
     * @return Collection<int, WorkTime>
     */
    public function getWorkTimes(): Collection
    {
        return $this->workTimes;
    }

    /**
     * Add work time
     *
     * @param WorkTime $workTime
     * @return void
     */
    public function addWorkTime(WorkTime $workTime): void
    {
        if (!$this->workTimes->contains($workTime)) {
            $this->workTimes->add($workTime);
            $workTime->setEmployee($this);
        }
    }

    /**
     * Remove work time
     *
     * @param WorkTime $workTime
     * @return void
     */
    public function removeWorkTime(WorkTime $workTime): void
    {
        if ($this->workTimes->removeElement($workTime)) {
            if ($workTime->getEmployee() === $this) {
                $workTime->setEmployee(null);
            }
        }
    }
}
