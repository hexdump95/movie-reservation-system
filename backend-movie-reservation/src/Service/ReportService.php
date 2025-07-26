<?php

namespace App\Service;

use App\Repository\ReportRepository;

class ReportService
{
    private ReportRepository $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function getRevenueGroupedByShowtime(): ?array
    {
        return $this->reportRepository->getRevenueGroupByMonth();
    }

    public function getRevenueByMonthGroupedByShowtime(string $date): ?array
    {
        return $this->reportRepository->getRevenueByMonthGroupedByShowtime($date);
    }

}
