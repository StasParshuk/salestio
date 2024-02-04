<?php

namespace App\Service;

use App\Dto\Ruquest\CartCalculateItemDto;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CartCalculationService
{
    private string $openexchageretesApId;
    public function __construct(private ClientInterface $client,private ContainerBagInterface $bag)
    {
        $this->openexchageretesApId = $this->bag->get('OPENEXCHANGERETES_ID');
    }


    public function getCheckout(CartCalculateItemDto $calculateItemDto): array
    {
        $response = $this->sendRequestLatest($calculateItemDto->getCheckoutCurrency());
        return [
            'checkoutPrice' => $this->cartCalculate($calculateItemDto, $response),
            'checkoutCurrency' => $calculateItemDto->getCheckoutCurrency()
        ];
    }

    public function cartCalculate(CartCalculateItemDto $calculateItemDto, array $response): int|float
    {
        $total = 0;
        foreach ($calculateItemDto->getItems() as $cartItem) {
            $totalInCurrentCartItem = $cartItem['price'] * $cartItem['quantity'];
            $currentCurrnecy = $response['rates'][$cartItem['currency']];
            if ($currentCurrnecy === 1) {
                $total += $totalInCurrentCartItem;
            } else {
                $total += $totalInCurrentCartItem / $currentCurrnecy;
            }

        }
        return round($total,2);
    }

    /**
     * @param string $base валюта в которой надо выдать результат
     * @return array
     * @throws GuzzleException
     * @throws \JsonException
     */
    private function sendRequestLatest(string $base): array
    {
        $params = [
            'query' => [
                'app_id' => $this->openexchageretesApId,
                'base' => $base,
            ]
        ];
        $response = $this->client->request("GET", '/latest.json', $params);
        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
            //тут бы по хорошему десериализовать в обьект и работать с ним не белаю чтобы времени много не тратить
            //return $this->serializer->deserialize($content,ArrayDenormalizer::class ,'json', );
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }
        throw  new \Exception('Ошибка ответа от апи OPENEXCHANGERETES');
        // логирование вывод ошибки и.т.д
    }
    public function sendRequestCurrencies( ): array
    {
        $params = [
            'query' => [
                'app_id' => $this->openexchageretesApId,
            ]
        ];
        $response = $this->client->request("GET", '/currencies.json', $params);
        if ($response->getStatusCode() === 200) {
            $content = $response->getBody()->getContents();
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }
        throw  new \Exception('Ошибка ответа от апи OPENEXCHANGERETES');
        // логирование вывод ошибки и.т.д
    }

}
