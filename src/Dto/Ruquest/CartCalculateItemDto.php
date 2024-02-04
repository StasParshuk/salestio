<?php

namespace App\Dto\Ruquest;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;

class CartCalculateItemDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[CustomAssert\CartItem]
        public readonly array $items,

        #[Assert\Choice(['USD', 'EUR'])]
        public readonly string $checkoutCurrency,
    ) {
    }
}
