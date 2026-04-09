<?php

namespace App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    #[Route('/api/properties', name: 'api_properties_list', methods: ['GET'])]
    public function list(PropertyRepository $repository): JsonResponse
    {
        $properties = $repository->findAll();
        return $this->json(
            $properties, 
            200, 
            [], 
            ['groups' => ['property:list']]
        );
    }

    #[Route('/api/properties/{id}', name: 'api_properties_show', methods: ['GET'])]
    public function show(PropertyRepository $repository, int $id): JsonResponse
    {
        $property = $repository->find($id);

        if (!$property) {
            return $this->json([
                'message' => 'Imóvel não encontrado',
                'code' => 404
            ], 404);
        }

        return $this->json($property, 200, [], [
            'groups' => ['property:list']
        ]);
    }
}