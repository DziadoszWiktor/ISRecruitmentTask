<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\DuplicatedWorkTimeException;
use App\Exception\WorkTimeExceededException;
use App\Service\WorkTimeService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/work-times', name: 'api_work_times_')]
class WorkTimeController extends AbstractController
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i';

    /**
     * WorkTimeController construct
     *
     * @param WorkTimeService $workTimeService
     */
    public function __construct(
        private readonly WorkTimeService $workTimeService
    ) {}

    /**
     * Work time endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $requestContent = json_decode($request->getContent(), true);
        $employeeId = $requestContent['employeeId'] ?? null;
        $startRaw = $requestContent['startedAt'] ?? null;
        $endRaw = $requestContent['endedAt'] ?? null;

        $errors = [];
        if (!$employeeId) { $errors[] = 'employeeId is required'; }
        if (!$startRaw) { $errors[] = 'startedAt is required'; }
        if (!$endRaw) { $errors[] = 'endedAt is required'; }

        if ($errors) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $startedAt = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $startRaw);
            $endedAt = DateTimeImmutable::createFromFormat(self::DATE_TIME_FORMAT, $endRaw);
            if (!$startedAt || !$endedAt) {
                throw new WorkTimeExceededException('Invalid date format! Expected:' . self::DATE_TIME_FORMAT);
            }

            $this->workTimeService->register($employeeId, $startedAt, $endedAt);

            return $this->json(
                ['data' => 'Work Time added!'],
                Response::HTTP_CREATED
            );

        } catch (WorkTimeExceededException|DuplicatedWorkTimeException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
