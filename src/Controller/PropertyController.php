<?php

namespace App\Controller;

use App\Entity\Property;
use App\Entity\PropertyImage;
use App\Repository\PropertyRepository;
use App\Dto\PropertySearchDto;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PropertyController extends AbstractController
{
    #[Route('/api/properties', name: 'api_properties_list', methods: ['GET'])]
    public function list(PropertyRepository $repository, #[MapQueryString] ?PropertySearchDto $searchDto): JsonResponse 
    {    
        $searchDto ??= new PropertySearchDto();

        $properties = $repository->findByFilters($searchDto);

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
    
    #[Route('/api/properties', name: 'api_properties_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator) : JsonResponse 
    {
        $property = $serializer->deserialize($request->getContent(), Property::class, 'json');

        $errors = $validator->validate($property);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $em->persist($property);
        $em->flush();

        return $this->json($property, 201, [], ['groups' => ['property:list']]);
    }

    #[Route('/api/properties/{id}', name: 'api_properties_update', methods: ['PUT'])]    
    public function update(int $id, PropertyRepository $repository, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse {
        $property = $repository->find($id);

        if (!$property) {
            return $this->json(['message' => 'Imóvel não encontrado'], 404);
        }       
        
        $errors = $validator->validate($property);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $serializer->deserialize(
            $request->getContent(), 
            Property::class, 
            'json', 
            [AbstractNormalizer::OBJECT_TO_POPULATE => $property]
        );

        $em->flush();

        return $this->json($property, 200, [], ['groups' => ['property:list']]);
    }

    #[Route('/api/properties/{id}', name: 'api_properties_delete', methods: ['DELETE'])]
    public function delete(int $id, PropertyRepository $repository, EntityManagerInterface $em): JsonResponse
    {
        $property = $repository->find($id);

        if (!$property) {
            return $this->json(['message' => 'Imóvel não encontrado'], 404);
        }

        $em->remove($property);
        $em->flush();

        return $this->json(null, 204);
    }

    #[Route('/api/properties/{id}/images', name: 'api_properties_add_image', methods: ['POST'])]
    public function addImage(
        int $id, 
        PropertyRepository $repository, 
        Request $request, 
        EntityManagerInterface $em, 
        FileUploader $uploader
    ): JsonResponse {
        $property = $repository->find($id);

        if (!$property) {
            return $this->json(['message' => 'Imóvel não encontrado'], 404);
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('image');

        if (!$file) {
            return $this->json(['message' => 'Nenhum arquivo enviado'], 400);
        }

        $newFilename = $uploader->upload($file);

        $propertyImage = new PropertyImage();
        $propertyImage->setPath($newFilename);
        $propertyImage->setPropertyId($property);
        $propertyImage->setAltText('Imagem de ' . $property->getTitle());

        $em->persist($propertyImage);
        $em->flush();

        return $this->json($propertyImage, 201, [], ['groups' => ['property:list']]);
    }
}