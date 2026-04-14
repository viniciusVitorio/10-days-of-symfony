<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\PropertySearchDto;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function findByFilters(PropertySearchDto $filters): array
    {
        $qb = $this->createQueryBuilder('property')
            ->leftJoin('property.propertyImages', 'pi')
            ->addSelect('pi')
            ->andWhere('property.isPublished = :published')
            ->setParameter('published', true);

        if ($filters->type) {
            $qb->andWhere('property.type = :type')
            ->setParameter('type', $filters->type);
        }

        if ($filters->minPrice) {
            $qb->andWhere('property.price >= :minPrice')
            ->setParameter('minPrice', $filters->minPrice);
        }

        $qb->setFirstResult(($filters->page - 1) * $filters->limit)
        ->setMaxResults($filters->limit)
        ->orderBy('property.createdAt', 'DESC');

        return [
            'data' => $qb->getQuery()->getResult(),
            'meta' => [
                'total' => count($this->findAll()),
                'page' => $filters->page,
                'limit' => $filters->limit
            ]
        ];
    }

    public function getDashboardStats(): array
    {
        $statusStats = $this->createQueryBuilder('p')
            ->select('p.status, COUNT(p.id) as count')
            ->groupBy('p.status')
            ->getQuery()
            ->getResult();

        $metrics = $this->createQueryBuilder('p')
            ->select('SUM(p.price) as totalValue, AVG(p.price) as avgPrice, COUNT(p.id) as totalItems')
            ->getQuery()
            ->getSingleResult();

        $topProperties = $this->createQueryBuilder('p')
            ->select('p.id, p.title, p.price')
            ->orderBy('p.price', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        return [
            'byStatus' => $statusStats,
            'financial' => [
                'total_value' => (float) $metrics['totalValue'],
                'avg_price' => (float) $metrics['avgPrice'],
                'total_items' => (int) $metrics['totalItems'],
            ],
            'top_expensive' => $topProperties
        ];
    }
}
