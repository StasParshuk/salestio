<?php

namespace App\Tests\UnitTests;

use App\Dto\Ruquest\CartCalculateItemDto;
use App\Service\CartCalculationService;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CartCalculateTest extends TestCase
{
    public function testCartCalculate(): void
    {
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
            ],
        ];

        $result = $service->cartCalculate($calculateItemDto, $response);

        $expectedResult = (49.99 * 2 / 0.85) + (12 * 3); // Expected total in USD
        $expectedResult = round($expectedResult,2);
        $this->assertEquals($expectedResult, $result);
    }

    public function testSendRequestCurrenciesSuccess(): void
    {
        $expectedCurrencies = [
            'USD' => 'United States Dollar',
            'EUR' => 'Euro',
        ];

        $responseBody = json_encode($expectedCurrencies);
        $responseMock = new Response(200, [], $responseBody);

        $clientMock = $this->createMock(ClientInterface::class);
        $clientMock->method('request')
            ->willReturn($responseMock);

        $containerBagMock = $this->createMock(ContainerBagInterface::class);
        $containerBagMock->method('get')
            ->willReturn('YOUR_OPENEXCHANGERATES_API_ID');

        $service = new CartCalculationService($clientMock, $containerBagMock);

        $result = $service->sendRequestCurrencies();
        $this->assertEquals($expectedCurrencies, $result);
    }
}
