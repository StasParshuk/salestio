<?php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class CartCalculationService
{
    public function __construct(private ClientInterface $client)
    {
    }

    public function calculate(){

    }

}
