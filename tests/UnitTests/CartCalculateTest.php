<?php

namespace App\Tests\UnitTests;

use App\Dto\Ruquest\CartCalculateItemDto;
use App\Service\CartCalculationService;
use GuzzleHttp\ClientInterface;
use http\Client\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CartCalculateTest extends TestCase
{
    public function testCartCalculate(): void
    {
        // Arrange
        $clientMock = $this->createMock(ClientInterface::class);
        $bagMock = $this->createMock(ContainerBagInterface::class);
        $bagMock->method('get')->willReturn('YOUR_OPENEXCHANGERATES_API_ID');

        $service = new CartCalculationService($clientMock, $bagMock);
        $calculateItemDto = new CartCalculateItemDto([
                ['currency' => 'EUR', 'price' => 49.99, 'quantity' => 2],
                ['currency' => 'USD', 'price' => 12, 'quantity' => 3],
            ], 'USD');
        $response = [
            'rates' => [
                'EUR' => 0.85, // EUR to USD exchange rate
                'USD' => 1,    // USD to USD exchange rate (always 1)
            ]
        ];

        // Act
        $result = $service->cartCalculate($calculateItemDto, $response);

        // Assert
        $expectedResult = (49.99 * 2 / 0.85) + (12 * 3); // Expected total in USD
        $this->assertEquals($expectedResult, $result);
    }

}
