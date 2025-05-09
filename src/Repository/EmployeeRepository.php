<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;
use App\Interface\EmployeeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository implements EmployeeRepositoryInterface
{
    /**
     * EmployeeRepository construct
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    /**
     * Save employee
     *
     * @param Employee $employee
     * @return void
     */
    public function save(Employee $employee): void
    {
        $em = $this->getEntityManager();
        $em->persist($employee);
        $em->flush();
    }
}
