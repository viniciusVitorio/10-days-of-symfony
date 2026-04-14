<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/api/dashboard/stats', name: 'api_dashboard_stats', methods: ['GET'])]
    public function getStats(PropertyRepository $repository): JsonResponse
    {
        $stats = $repository->getDashboardStats();

        return $this->json($stats);
    }
}