<?php

namespace App\Controller;

use App\Dto\Ruquest\CartCalculateItemDto;
use App\Service\CartCalculationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

#[Route('/api/v1/cart')]
class CartController extends AbstractController
{
    #[Route('/calculate', name: 'app_cart_calculate',methods: "POST")]
    public function calculate(#[MapRequestPayload] CartCalculateItemDto $calculateItemDto,CartCalculationService $calculationService): Response
    {
        $calculationService->calculate($calculateItemDto);
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
