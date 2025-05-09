<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\InvalidDateFormatException;
use App\Service\WorkTimeSummaryService;
use DateMalformedStringException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/work-time-summary', name: 'work_time_summary')]
class WorkTimeSummaryController extends AbstractController
{
    /**
     * WorkTimeSummaryController construct
     *
     * @param WorkTimeSummaryService $summaryService
     */
    public function __construct(
        private readonly WorkTimeSummaryService $summaryService
    ) {}

    /**
     * Summary endpoint
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('', name: 'work_time_summary_summary', methods: ['GET'])]
    public function summary(Request $request): JsonResponse
    {
        $employeeId = $request->query->get('employeeId');
        $date = $request->query->get('date');

        if (!$employeeId || !$date) {
            return $this->json(
                ['error' => 'Missing parameters: employeeId and date are required'],
                400
            );
        }

        if (!Uuid::isValid($employeeId)) {
            return $this->json(['error' => 'employeeId is not a valid UUID'], 400);
        }

        try {
            if ($this->matches('/^\d{4}-\d{2}$/', $date)) {
                $data = $this->summaryService->summarizeMonth($employeeId, $date);
                $response = [
                    'standard_hours' => $data['normal_hours'],
                    'standard_rate'  => $data['normal_rate'],
                    'overtime_hours' => $data['overtime_hours'],
                    'overtime_rate'  => $data['overtime_rate'],
                    'total_amount'   => $data['total'],
                ];
            } elseif ($this->matches('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $data = $this->summaryService->summarizeDay($employeeId, $date);
                $response = [
                    'hours'        => $data['hours'],
                    'hourly_rate'  => $data['rate'],
                    'total_amount' => $data['total'],
                ];
            } else {
                throw new InvalidDateFormatException('Date format must be Y-m or Y-m-d');
            }

            return $this->json(['response' => $response]);
        } catch (InvalidDateFormatException|DateMalformedStringException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Verify date
     *
     * @param string $pattern
     * @param string $date
     * @return bool
     */
    private function matches(string $pattern, string $date): bool
    {
        return (bool) preg_match($pattern, $date);
    }
}
