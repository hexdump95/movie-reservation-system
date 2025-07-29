<?php

namespace App\Controller;

use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/reports')]
#[IsGranted("ROLE_ADMIN")]
class ReportController extends AbstractController
{
    private ReportService $reportService;
    private SerializerInterface $serializer;

    public function __construct(ReportService $reportService, SerializerInterface $serializer)
    {
        $this->reportService = $reportService;
        $this->serializer = $serializer;
    }

    #[Route('/revenue', name: 'get_revenue', methods: ['GET'])]
    public function getRevenue(): JsonResponse
    {
        $report = $this->reportService->getRevenueGroupedByShowtime();
        return new JsonResponse(
            $this->serializer->normalize($report),
            Response::HTTP_OK
        );
    }

    #[Route('/revenue-by-month', name: 'get_revenue_by_month', methods: ['GET'])]
    public function getRevenueByMonth(#[MapQueryParameter] string $date = ''): JsonResponse
    {
        if ($date == '') {
            return new JsonResponse(
                ['success' => false],
                Response::HTTP_BAD_REQUEST
            );
        }
        $report = $this->reportService->getRevenueByMonthGroupedByShowtime($date);
        return new JsonResponse(
            $this->serializer->normalize($report),
            Response::HTTP_OK
        );
    }
}
