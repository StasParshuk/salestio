<?php

namespace App\Service;

use App\Dto\Ruquest\CartCalculateItemDto;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class CartCalculationService
{
    private string $openexchageretesToken;
    public function __construct(private ClientInterface $client,private ContainerBagInterface $bag)
    {
        $this->openexchageretesApId = $this->bag->get('OPENEXCHANGERETES_ID');
    }

    public function calculate(CartCalculateItemDto $calculateItemDto)
    {
        $response = $this->sendRequest($calculateItemDto->getCheckoutCurrency());

        foreach ($calculateItemDto->getItems() as $cartItem) {
            dd($cartItem, $response, $calculateItemDto);
        }
    }

    /**
     * @param string $base валюта в которой надо выдать результат
     * @return array
     * @throws GuzzleException
     * @throws \JsonException
     */
    private function sendRequest(string $base): array
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

}
