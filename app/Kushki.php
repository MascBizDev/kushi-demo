<?php

namespace App;

use Illuminate\Support\Facades\Http;

class Kushki
{
    private $publicKey, $privateKey, $headers;

    public function __construct()
    {
        $this->publicKey = config('kushki.public_key');
        $this->privateKey = config('kushki.private_key');

        $this->headers = [
            'Public-Merchant-Id' => $this->publicKey,
            'Private-Merchant-Id' => $this->privateKey,
            'Content-Type' => 'application/json'
        ];
    }

    public function chargeToken($amount)
    {
        $response = Http::withHeaders(
            $this->headers
        )->post('https://api-uat.kushkipagos.com/card/v1/tokens', [
            "card" => [
                "name" => "TESTING",
                "number" => "4386261181077714",
                "expiryMonth" => "08",
                "expiryYear" => "23",
                "cvv" => "121"
            ],
            "totalAmount" => $amount,
            "currency" => "USD"
        ]);

        return $response;
    }

    public function charge($amount = 100)
    {
        $token = $this->chargeToken($amount)->json()['token'];

        # Notes:
        # The amount that will be charged must be equal to the token when is requested
        $response = Http::withHeaders(
            $this->headers
        )->post('https://api-uat.kushkipagos.com/card/v1/charges', [
            "token" => $token,
            "amount" => [
                "subtotalIva" => 0,
                "subtotalIva0" => $amount,
                "ice" => 0,
                "iva" => 0,
                "currency" => "USD"
            ],
            "deferred" => [
                "graceMonths" => "00",
                "creditType" => "01",
                "months" => 3
            ],
            "metadata" => [
                "contractID" => "157AB"
            ],
            "fullResponse" => true
        ]);

        return $response;
    }

    /**
     * Request token for subscriptions
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function subscriptionToken()
    {
        $response = Http::withHeaders(
            $this->headers
        )->post('https://api-uat.kushkipagos.com/subscriptions/v1/card/tokens', [
            "card" => [
                "name" => "Pruebas",
                "number" => "4242424242424242",
                "expiryMonth" => "03",
                "expiryYear" => "19",
                "cvv" => "071"
            ],
            "currency" => "USD"
        ]);

        return $response;
    }

    public function subscription($amount = 100)
    {
        $token = $this->subscriptionToken()->json()['token'];

        $response = Http::withHeaders(
            $this->headers
        )->post('https://api-uat.kushkipagos.com/subscriptions/v1/card', [
            "token" => $token,
            "planName" => "Premium",
            "periodicity" => "daily",
            "contactDetails" => [
                "firstName" => "Juan",
                "lastName" => "Pruebas",
                "email" => "pruebas@kushki.com",
            ],
            "amount" => [
                "subtotalIva" => $amount,
                "subtotalIva0" => 0,
                "ice" => 0,
                "iva" => 0.14,
                "currency" => "USD",
            ],
            "startDate" => "2018-09-25",
            "language" => "es",
            "metadata" => [
                "plan" => [
                    "description" => [
                        "fitness" => [
                            "cardio" => "include",
                            "rumba" => "include",
                            "pool" => "include",
                        ],
                    ],
                ],
            ],
        ]);

        return $response;
    }

    public function cancel()
    {
        $subscriptionId = $this->subscription()->json()['subscriptionId'];

        $response = Http::withHeaders(
            $this->headers
        )->post("https://api-uat.kushkipagos.com/subscriptions/v1/card/${subscriptionId}");

        return $response;
    }
}
