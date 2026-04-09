<?php

namespace App\DataFixtures;

use App\Entity\Property;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\PropertyImage;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $property = new Property();
            $property->setTitle("Imóvel de Teste $i");
            $property->setSlug("imovel-de-teste-$i");
            $property->setDescription("Descrição $i");
            $property->setPrice(250000 + ($i * 1000));
            $property->setType('Casa'); 
            $property->setStatus('Disponível');
            $property->setIsPublished(true);
            $property->setCreatedAt(new \DateTimeImmutable());

            for ($j = 1; $j <= 3; $j++) {
                $image = new PropertyImage();
                $image->setPath("imagem $j");
                $image->setAltText("Imagem $j do imóvel $i");
                $image->setSetOrder($j);
                
                $image->setPropertyId($property); 
                
                $manager->persist($image);
            }

            $manager->persist($property);
        }

        $manager->flush();
    }
}
