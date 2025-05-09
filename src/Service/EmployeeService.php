<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Employee;
use App\Interface\EmployeeRepositoryInterface;

class EmployeeService
{
    /**
     * EmployeeService construct
     *
     * @param EmployeeRepositoryInterface $employeeRepository
     */
    public function __construct(
        private readonly EmployeeRepositoryInterface $employeeRepository
    ) {}

    /**
     * Create new employee
     *
     * @param string $firstName
     * @param string $lastName
     * @return Employee
     */
    public function create(string $firstName, string $lastName): Employee
    {
        $employee = new Employee();
        $employee->setName($firstName);
        $employee->setSurname($lastName);
        $this->employeeRepository->save($employee);

        return $employee;
    }
}
