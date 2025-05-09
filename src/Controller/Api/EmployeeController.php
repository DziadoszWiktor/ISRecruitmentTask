<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/employees', name: 'api_employees_')]
class EmployeeController extends AbstractController
{
    /**
     * EmployeeController construct
     *
     * @param EmployeeService $employeeService
     */
    public function __construct(
        private readonly EmployeeService $employeeService
    ) {}

    /**
     * Create Employee endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);

        $firstName = $requestContent['firstName'] ?? null;
        $lastName  = $requestContent['lastName']  ?? null;

        $errors = [];
        if (!$firstName) {
            $errors[] = 'firstName is required';
        }
        if (!$lastName) {
            $errors[] = 'lastName is required';
        }

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $employee = $this->employeeService->create($firstName, $lastName);

        return $this->json(
            ['data' => ['id' => (string)$employee->getId()]],
            Response::HTTP_CREATED
        );
    }
}
