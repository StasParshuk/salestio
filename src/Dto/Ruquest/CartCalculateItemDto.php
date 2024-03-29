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

        #[Assert\Choice(['USD'],message: "в бесплатной версии ток usd можно отправить")]
        public readonly string $checkoutCurrency,
    ) {
    }

    public function getCheckoutCurrency(): string
    {
        return $this->checkoutCurrency;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
