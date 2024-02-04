<?php

namespace App\Tests\WebTests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testCalculateCart(): void
    {
        $client = static::createClient();
        $data = [
            "items" => [
                "42" => [
                    "currency" => "EUR",
                    "price" => 49.99,
                    "quantity" => 2
                ],
                "55" => [
                    "currency" => "USD",
                    "price" => 12,
                    "quantity" => 3
                ]
            ],
            "checkoutCurrency" => "USD"
        ];
        $client->request('POST', '/api/v1/cart/calculate', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('checkoutPrice', $responseData);
        $this->assertArrayHasKey('checkoutCurrency', $responseData);
        $this->assertEquals('USD', $responseData['checkoutCurrency']);
    }

    public function testCalculateCartWithInvalidData(): void
    {
        $client = static::createClient();
        $data = [
            "invalid" => "data"
        ];
        $client->request('POST', '/api/v1/cart/calculate', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $responseStatusCode = $client->getResponse()->getStatusCode();
        $expectedFirstDigit = 4;

        $this->assertEquals($expectedFirstDigit, (int)($responseStatusCode / 100));

    }
}
