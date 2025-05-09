<?php

namespace App\Interface;

use App\Entity\Employee;

interface EmployeeRepositoryInterface
{
    /**
     * Save employee
     *
     * @param Employee $employee
     * @return void
     */
    public function save(Employee $employee): void;
}