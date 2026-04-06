<?php 

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthController
{
    #[Route('/health', methods: ['GET'])]
    public function health(): JsonResponse
    {
        $health = [
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    
        return new JsonResponse($health);
    }
}