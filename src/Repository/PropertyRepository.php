<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function findByFilters(array $filters, int $page = 1, int $limit = 10): array
    {
        $queryBuilder = $this->createQueryBuilder('property')
            ->leftJoin('property.propertyImages', 'pi')
            ->addSelect('pi')
            ->andWhere('property.isPublished = :published')
            ->setParameter('published', true);

        if (!empty($filters['type'])) {
            $queryBuilder->andWhere('property.type = :type')
            ->setParameter('type', $filters['type']);
        }

        if (!empty($filters['minPrice'])) {
            $queryBuilder->andWhere('property.price >= :minPrice')
            ->setParameter('minPrice', $filters['minPrice']);
        }

        $offset = ($page - 1) * $limit;

        $queryBuilder->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('property.createdAt', 'DESC');

        $totalItems = count($this->findAll());

        return [
            'data' => $queryBuilder->getQuery()->getResult(),
            'meta' => [
                'total' => $totalItems,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($totalItems / $limit)
            ]
        ];
    }
}
