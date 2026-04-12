<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PropertySearchDto
{
    public function __construct(
        #[Assert\Type('string')]
        public readonly ?string $type = null,

        #[Assert\Type(type: ['float', 'numeric'], message: "Preço inválido")]
        #[Assert\PositiveOrZero]
        public readonly mixed $minPrice = null,

        #[Assert\Type(type: ['float', 'numeric'])]
        #[Assert\PositiveOrZero]
        public readonly mixed $maxPrice = null,
        #[Assert\Positive]
        public readonly int $page = 1,

        #[Assert\Positive]
        #[Assert\LessThanOrEqual(50)]
        public readonly int $limit = 10,
    ) {
    }
}