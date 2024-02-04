<?php

namespace App\Validator;

use App\Service\CartCalculationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartItemValidator extends ConstraintValidator
{
    public function __construct(private CartCalculationService $calculationService)
    {
    }

    public function validate($value, Constraint $constraint)
    {


        /* @var CartItem $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                //валидация обязательных полей
                if (!array_key_exists('currency', $item)) {
                    $this->context->buildViolation('currency required')
                        ->atPath('currency')
                        ->addViolation();
                }

                if (!array_key_exists('price', $item)) {
                    $this->context->buildViolation('price required')
                        ->atPath('price')
                        ->addViolation();
                }

                if (!array_key_exists('quantity', $item)) {
                    $this->context->buildViolation('quantity required')
                        ->atPath('quantity')
                        ->addViolation();
                }

                if (isset($item['currency']) && !array_key_exists($item['currency'], $this->calculationService->sendRequestCurrencies())) {
                    $this->context->buildViolation('in cart not correct value currency')
                        ->atPath('currency')
                        ->addViolation();
                }

                if (isset($item['price']) && (!is_numeric($item['price']))) {
                    $this->context->buildViolation('price not valid')
                        ->atPath('price')
                        ->addViolation();
                }

                if (isset($item['quantity']) && !is_int($item['quantity'])) {
                    $this->context->buildViolation('quantity not valid')
                        ->atPath('quantity')
                        ->addViolation();
                }
            }
        }
    }
}
